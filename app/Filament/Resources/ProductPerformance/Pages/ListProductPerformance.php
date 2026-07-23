<?php

namespace App\Filament\Resources\ProductPerformance\Pages;

use App\Filament\Exports\ProductPerformanceExport;
use App\Filament\Resources\ProductPerformance\ProductPerformanceResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListProductPerformance extends ListRecords
{
    protected static string $resource = ProductPerformanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return Excel::download(new ProductPerformanceExport(), 'product-performance-export.xlsx');
                }),
        ];
    }
}
