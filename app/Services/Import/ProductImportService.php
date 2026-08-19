<?php

namespace App\Services\Import;

use App\Models\ProductImage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductImportService
{
    protected BrandResolver $brandResolver;
    protected CategoryResolver $categoryResolver;
    protected ProductResolver $productResolver;
    protected VariantResolver $variantResolver;
    protected ImageResolver $imageResolver;
    protected PriceCalculator $priceCalculator;
    protected ImportLogger $logger;

    protected bool $warmed = false;

    public function __construct(
        BrandResolver $brandResolver,
        CategoryResolver $categoryResolver,
        ProductResolver $productResolver,
        VariantResolver $variantResolver,
        ImageResolver $imageResolver,
        PriceCalculator $priceCalculator,
        ImportLogger $logger
    ) {
        $this->brandResolver = $brandResolver;
        $this->categoryResolver = $categoryResolver;
        $this->productResolver = $productResolver;
        $this->variantResolver = $variantResolver;
        $this->imageResolver = $imageResolver;
        $this->priceCalculator = $priceCalculator;
        $this->logger = $logger;
    }

    /**
     * Warm all resolver caches. Should be called once before processing rows.
     */
    public function warmCaches(): void
    {
        if ($this->warmed) {
            return;
        }

        $this->brandResolver->warmCache();
        $this->categoryResolver->warmCache();
        $this->productResolver->warmCache();
        $this->variantResolver->warmCache();

        $this->warmed = true;
    }

    /**
     * Process a single import row.
     *
     * @param array<string, mixed> $row
     * @return void
     * @throws \Exception On critical validation failure
     */
    public function processRow(array $row): void
    {
        $this->ensureVariantColumns();
        $this->warmCaches();
        $this->logger->incrementTotalRows();

        try {
            // Validate required fields
            $this->validateRow($row);

            // Resolve Brand
            $brand = $this->brandResolver->resolve($row['brand']);

            // Resolve Category + optional Subcategory
            $parentCategory = $this->categoryResolver->resolveParent($row['category']);
            $subCategoryName = trim((string) ($row['sub_category'] ?? ''));
            $category = $subCategoryName !== ''
                ? $this->categoryResolver->resolveChild($subCategoryName, $parentCategory)
                : $parentCategory;

            // Parse delivery flags
            $allowsCourier = $this->parseTruthy($row['courier'] ?? null);

            // Resolve Featured Image
            $featuredImagePath = null;
            if (array_key_exists('featured_image', $row) && $row['featured_image'] !== null && $row['featured_image'] !== '') {
                // Directly save it into the database under products/ directory
                $featuredImagePath = 'products/' . basename($row['featured_image']);

                /*
                // Old resolution logic (preserved for future use):
                $existingProduct = \App\Models\Product::where('sku', trim($row['product_sku']))->first();
                $existingFeatured = $existingProduct?->featured_image;

                if ($existingFeatured) {
                    $existingFeaturedBase = basename($existingFeatured);
                    $existingFeaturedNoExt = pathinfo($existingFeatured, PATHINFO_FILENAME);
                    $newFeaturedBase = basename($row['featured_image']);
                    $newFeaturedNoExt = pathinfo($row['featured_image'], PATHINFO_FILENAME);

                    if (strcasecmp($existingFeaturedBase, $newFeaturedBase) === 0 || strcasecmp($existingFeaturedNoExt, $newFeaturedNoExt) === 0) {
                        $featuredImagePath = $existingFeatured;
                    }
                }

                if (!$featuredImagePath) {
                    $resolved = $this->imageResolver->resolve($row['featured_image']);
                    if ($resolved) {
                        $featuredImagePath = $resolved;
                    } else {
                        $this->logger->addWarning("Missing featured image: {$row['featured_image']} for product {$row['product_sku']}");
                        $this->logger->incrementMissingImages();
                    }
                }
                */
            }

            // Resolve Product
            $productAttributes = [
                'sku' => trim($row['product_sku']),
                'name' => trim($row['product_name']),
                'brand_id' => $brand->id,
                'category_id' => $category->id,
                'supplier_code' => $row['supplier_code'] ?? null,
                'key_features' => $this->cleanRichText($row['key_features'] ?? null),
                'description' => $this->cleanRichText($row['product_description'] ?? null),
                'search_keywords' => $row['search_keywords'] ?? null,
                'is_active' => true,
                'requires_direct_delivery' => true,
                'allows_courier' => $allowsCourier,
                'meta_title' => $row['seo_title'] ?? null,
                'meta_description' => $row['seo_description'] ?? null,
            ];

            if ($featuredImagePath !== null) {
                $productAttributes['featured_image'] = $featuredImagePath;
            }

            $productResult = $this->productResolver->resolve($productAttributes);
            $product = $productResult['product'];

            if ($productResult['is_new']) {
                $this->logger->incrementImportedProducts();
            } else {
                $this->logger->incrementUpdatedProducts();
            }

            // Calculate selling price
            $buyingPrice = (float) ($row['buying_price'] ?? 0);
            $gstPercent = $this->priceCalculator->parseGst($row['gst'] ?? null);
            $marginPercent = (float) ($row['margin'] ?? 0);
            
            // If selling_price is provided directly in the Excel row, use it.
            // Otherwise, calculate it using the compounded formula (same as admin form).
            if (isset($row['selling_price']) && (float)$row['selling_price'] > 0) {
                $sellingPrice = round((float)$row['selling_price'], 2);
            } else {
                $sellingPrice = $this->priceCalculator->calculate($buyingPrice, $marginPercent, $gstPercent);
            }

            $strikedPrice = null;
            if (array_key_exists('striked_price', $row) && $row['striked_price'] !== null && $row['striked_price'] !== '') {
                $strikedPrice = round((float) $row['striked_price'], 2);
            }

            // Resolve Variant
            $variantAttributes = [
                'product_id' => $product->id,
                'sku' => trim($row['variant_sku']),
                'unit' => $row['unit'] ?? null,
                'size' => $row['size'] ?? null,
                'buying_price' => $buyingPrice,
                'margin' => $marginPercent,
                'tax_percentage' => $gstPercent,
                'expiry_date' => $this->parseDateValue($row['expiry_date'] ?? null),
                'selling_price' => $sellingPrice,
                'striked_price' => $strikedPrice,
                'stock' => (int) ($row['stock'] ?? 0),
            ];

            $variantResult = $this->variantResolver->resolve($variantAttributes);

            if ($variantResult['is_new']) {
                $this->logger->incrementImportedVariants();
            } else {
                $this->logger->incrementUpdatedVariants();
            }

            // Resolve Additional Images
            if (array_key_exists('additional_images', $row) && $row['additional_images'] !== null && $row['additional_images'] !== '') {
                $requestedNames = array_filter(array_map('trim', explode(',', $row['additional_images'])));
                
                // Directly construct the paths under products/ directory
                $finalPaths = [];
                foreach ($requestedNames as $name) {
                    $finalPaths[] = 'products/' . basename($name);
                }

                // Delete existing ones not in $finalPaths
                ProductImage::where('product_id', $product->id)
                    ->whereNotIn('image_path', $finalPaths)
                    ->delete();

                // Insert only the new ones
                $existingImages = ProductImage::where('product_id', $product->id)->get();
                $existingPaths = $existingImages->pluck('image_path')->toArray();
                foreach ($finalPaths as $path) {
                    if (!in_array($path, $existingPaths)) {
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $path,
                        ]);
                    }
                }

                /*
                // Old resolution logic (preserved for future use):
                $existingImages = ProductImage::where('product_id', $product->id)->get();
                $finalPaths = [];
                $missingPaths = [];

                foreach ($requestedNames as $name) {
                    $nameBase = basename($name);
                    $nameNoExt = pathinfo($name, PATHINFO_FILENAME);

                    // Check if already exists in DB (either exact basename or name without extension matches)
                    $matched = $existingImages->first(function ($img) use ($nameBase, $nameNoExt) {
                        $existingBase = basename($img->image_path);
                        $existingNoExt = pathinfo($img->image_path, PATHINFO_FILENAME);
                        return strcasecmp($existingBase, $nameBase) === 0 || strcasecmp($existingNoExt, $nameNoExt) === 0;
                    });

                    if ($matched) {
                        $finalPaths[] = $matched->image_path;
                    } else {
                        // Resolve new image
                        $resolvedPath = $this->imageResolver->resolve($name);
                        if ($resolvedPath) {
                            $finalPaths[] = $resolvedPath;
                        } else {
                            $missingPaths[] = $name;
                        }
                    }
                }

                // Delete existing ones not in $finalPaths
                ProductImage::where('product_id', $product->id)
                    ->whereNotIn('image_path', $finalPaths)
                    ->delete();

                // Insert only the new ones
                $existingPaths = $existingImages->pluck('image_path')->toArray();
                foreach ($finalPaths as $path) {
                    if (!in_array($path, $existingPaths)) {
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $path,
                        ]);
                    }
                }

                // Log missing additional images
                if (count($missingPaths) > 0) {
                    $this->logger->addWarning("Missing " . count($missingPaths) . " additional image(s) for product {$row['product_sku']}: " . implode(', ', $missingPaths));
                    foreach ($missingPaths as $_) {
                        $this->logger->incrementMissingImages();
                    }
                }
                */
            }

        } catch (\Exception $e) {
            $this->logger->incrementFailedRows();
            $this->logger->addError("Row failed for SKU {$row['product_sku']}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Validate required fields for a row.
     */
    protected function parseDateValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_numeric($value)) {
            $numericValue = (float) $value;

            if ($numericValue > 1000000000) {
                return \Carbon\Carbon::createFromTimestamp((int) $numericValue)->format('Y-m-d');
            }

            if ($numericValue > 0) {
                if (class_exists(\PhpOffice\PhpSpreadsheet\Shared\Date::class)) {
                    try {
                        $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($numericValue);
                        if ($excelDate instanceof \DateTimeInterface) {
                            return $excelDate->format('Y-m-d');
                        }
                    } catch (\Throwable $e) {
                        // Fall back to a timestamp-based parse if the value is not an Excel serial date.
                    }
                }

                return \Carbon\Carbon::createFromTimestamp((int) $numericValue)->format('Y-m-d');
            }
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed === '') {
                return null;
            }

            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $trimmed)) {
                return $trimmed;
            }

            if (preg_match('/^\d{1,2}[\/\-.]\d{1,2}[\/\-.]\d{2,4}$/', $trimmed)) {
                $formats = ['d/m/Y', 'd-m-Y', 'd.m.Y', 'Y/m/d', 'Y-m-d', 'Y.m.d'];
                $parsed = null;

                foreach ($formats as $format) {
                    $candidate = \Carbon\Carbon::createFromFormat('!' . $format, $trimmed);
                    if ($candidate !== false) {
                        $parsed = $candidate;
                        break;
                    }
                }

                if ($parsed instanceof \DateTimeInterface) {
                    return $parsed->format('Y-m-d');
                }

                try {
                    return \Carbon\Carbon::parse($trimmed)->format('Y-m-d');
                } catch (\Throwable $e) {
                    return null;
                }
            }

            if (preg_match('/^\d+$/', $trimmed)) {
                return \Carbon\Carbon::createFromTimestamp((int) $trimmed)->format('Y-m-d');
            }

            try {
                return \Carbon\Carbon::parse($trimmed)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    public function ensureVariantColumns(): void
    {
        if (! Schema::hasTable('product_variants')) {
            return;
        }

        Schema::table('product_variants', function (Blueprint $table) {
            if (! Schema::hasColumn('product_variants', 'tax_percentage')) {
                $table->decimal('tax_percentage', 10, 2)->nullable()->after('margin');
            }

            if (! Schema::hasColumn('product_variants', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('tax_percentage');
            }

            if (! Schema::hasColumn('product_variants', 'selling_price')) {
                $table->decimal('selling_price', 10, 2)->nullable()->after('expiry_date');
            }

            if (! Schema::hasColumn('product_variants', 'striked_price')) {
                $table->decimal('striked_price', 10, 2)->nullable()->after('selling_price');
            }

            if (! Schema::hasColumn('product_variants', 'stock')) {
                $table->integer('stock')->default(0)->after('striked_price');
            }
        });
    }

    protected function validateRow(array $row): void
    {
        $required = ['product_sku', 'product_name', 'brand', 'category', 'variant_sku', 'buying_price'];

        foreach ($required as $field) {
            if (empty($row[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }
    }

    /**
     * Parse a truthy value (y, yes, true, 1) from Excel.
     */
    protected function parseTruthy(mixed $value): bool
    {
        if (is_null($value) || $value === '') {
            return false;
        }

        return in_array(strtolower(trim((string) $value)), ['y', 'yes', 'true', '1'], true);
    }

    /**
     * Get the logger instance for summary retrieval.
     */
    public function getLogger(): ImportLogger
    {
        return $this->logger;
    }

    protected function cleanRichText(?string $text): ?string
    {
        if ($text === null || $text === '') {
            return null;
        }

        $plainText = trim(html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $plainText = trim(str_replace(["\xc2\xa0", '&nbsp;'], ' ', $plainText));

        if ($plainText === '') {
            return null;
        }

        return trim($text);
    }
}
