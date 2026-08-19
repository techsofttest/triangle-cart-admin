<?php

namespace App\Filament\Exports;

use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromQuery, WithHeadings, WithMapping
{
    public function query(): Builder
    {
        return ProductVariant::query()
            ->with(['product.brand', 'product.category.parent', 'product.images'])
            ->whereHas('product')
            ->orderBy('product_id')
            ->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'Product SKU',
            'Product Name',
            'Brand',
            'Category',
            'Sub Category',
            'Variant SKU',
            'Supplier Code',
            'Unit',
            'Size',
            'Buying Price',
            'GST',
            'Margin %',
            'Selling Price',
            'Striked Price',
            'Stock',
            'Key Features',
            'Product Description',
            'Search Keywords',
            'Expiry Date',
            'Featured Image',
            'Additional Images',
            'SEO Title',
            'SEO Description',
            'Courier',
        ];
    }

    public function map($variant): array
    {
        $product = $variant->product;
        [$categoryName, $subCategoryName] = $this->resolveCategoryFields($product?->category);

        $additionalImages = $product?->images?->pluck('image_path')
            ->filter()
            ->map(fn ($imagePath) => basename((string) $imagePath))
            ->filter()
            ->join(', ');

        return [
            $product?->sku,
            $product?->name,
            $product?->brand?->name,
            $categoryName,
            $subCategoryName,
            $variant->sku,
            $product?->supplier_code,
            $variant->unit,
            $variant->size,
            $variant->buying_price,
            $variant->tax_percentage,
            $variant->margin,
            $variant->selling_price,
            $variant->striked_price,
            $variant->stock,
            $this->cleanRichText($product?->key_features),
            $this->cleanRichText($product?->description),
            $product?->search_keywords,
            $variant->expiry_date,
            $product?->featured_image ? basename($product->featured_image) : null,
            $additionalImages,
            $product?->meta_title,
            $product?->meta_description,
            $product?->allows_courier ? 'yes' : 'no',
        ];
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

    protected function resolveCategoryFields(?Category $category): array
    {
        if (! $category) {
            return [null, null];
        }

        if ($category->parent_id && $category->parent) {
            return [$category->parent->name, $category->name];
        }

        return [$category->name, null];
    }
}
