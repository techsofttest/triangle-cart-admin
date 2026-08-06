<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Imports\ProductImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\ToggleColumn;


class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([

                ImageColumn::make('featured_image')
                    ->label('Image')
                    ->disk('public')
                    ->size(50)
                    ->square()
                    ->visibility('public'),

                TextColumn::make('name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('supplier_code')
                    ->label('Supplier')
                    ->searchable(),
                    

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('brand.name')
                    ->label('Brand')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),

                /*TextColumn::make('variants_count')
                    ->label('Variants')
                    ->counts('variants')
                    ->sortable(),*/

                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),

                ToggleColumn::make('is_featured')
                    ->label('Featured')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistSortInSession()

            ->filters([
                //
            ])

            ->recordActions([
                EditAction::make(),
            ])

            ->headerActions([
                \Filament\Actions\Action::make('import_products')
                    ->label('Import Products')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('file')
                            ->label('Excel / CSV File')
                            ->required()
                            ->disk('local')
                            ->directory('imports'),
                    ])
                    ->action(function (array $data) {
                        $filePath = \Illuminate\Support\Facades\Storage::disk('local')->path($data['file']);

                        try {
                            $import = new \App\Imports\ProductExcelImport();
                            \Maatwebsite\Excel\Facades\Excel::import($import, $filePath);

                            $summary = $import->getService()->getLogger()->getFormattedSummary();

                            \Filament\Notifications\Notification::make()
                                ->title('Import Completed')
                                ->body($summary)
                                ->success()
                                ->send();
                        } finally {
                            // Delete uploaded temp file
                            if (\Illuminate\Support\Facades\Storage::disk('local')->exists($data['file'])) {
                                \Illuminate\Support\Facades\Storage::disk('local')->delete($data['file']);
                            }
                        }
                    }),
                \Filament\Actions\Action::make('import_images')
                    ->label('Import Images')
                    ->icon('heroicon-o-photo')
                    ->color('info')
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('file')
                            ->label('Single Image or ZIP File')
                            ->required()
                            ->disk('local')
                            ->directory('imports')
                            ->preserveFilenames()
                            ->acceptedFileTypes([
                                'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif',
                                'application/zip', 'application/x-zip-compressed', 'multipart/x-zip'
                            ]),
                    ])
                    ->action(function (array $data) {
                        $fileDisk = \Illuminate\Support\Facades\Storage::disk('local');
                        $filePath = $fileDisk->path($data['file']);
                        $mimeType = $fileDisk->mimeType($data['file']);
                        
                        $publicDisk = \Illuminate\Support\Facades\Storage::disk('public');

                        try {
                            if (in_array($mimeType, ['application/zip', 'application/x-zip-compressed', 'multipart/x-zip'])) {
                                $zip = new \ZipArchive();
                                if ($zip->open($filePath) === true) {
                                    $extractedCount = 0;
                                    for ($i = 0; $i < $zip->numFiles; $i++) {
                                        $filename = $zip->getNameIndex($i);
                                        $fileInfo = pathinfo($filename);
                                        
                                        // Skip directories and metadata or hidden files
                                        if (empty($fileInfo['extension']) || str_contains($filename, '__MACOSX') || str_starts_with($fileInfo['basename'], '.')) {
                                            continue;
                                        }

                                        $fileContent = $zip->getFromIndex($i);
                                        $destPath = 'products/' . $fileInfo['basename'];
                                        
                                        $publicDisk->put($destPath, $fileContent);
                                        $extractedCount++;
                                    }
                                    $zip->close();
                                    
                                    \Filament\Notifications\Notification::make()
                                        ->title('Images Extracted')
                                        ->body("Successfully extracted {$extractedCount} images into the products directory.")
                                        ->success()
                                        ->send();
                                } else {
                                    throw new \Exception("Could not open ZIP file.");
                                }
                            } else {
                                // Single image file
                                $basename = basename($filePath);
                                $destPath = 'products/' . $basename;
                                $publicDisk->put($destPath, file_get_contents($filePath));

                                \Filament\Notifications\Notification::make()
                                        ->title('Image Uploaded')
                                        ->body("Successfully uploaded {$basename} into the products directory.")
                                        ->success()
                                        ->send();
                            }
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Import Failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        } finally {
                            if ($fileDisk->exists($data['file'])) {
                                $fileDisk->delete($data['file']);
                            }
                        }
                    })
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

