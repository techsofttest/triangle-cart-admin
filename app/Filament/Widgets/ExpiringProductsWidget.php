<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Product;
use Carbon\Carbon;

class ExpiringProductsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 1;
    
    protected static ?int $sort = 2;
    
    protected static ?string $heading = 'Products Expiring Soon';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->whereNotNull('expiry_date')
                    ->whereBetween('expiry_date', [Carbon::today(), Carbon::today()->addDays(10)])
                    ->orderBy('expiry_date', 'asc')
                    ->limit(20)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Product Name'),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Expiring in')
                    ->formatStateUsing(function ($state) {
                        $days = Carbon::today()->startOfDay()->diffInDays(Carbon::parse($state)->startOfDay());
                        if ($days == 0) return 'Today';
                        if ($days == 1) return 'Tomorrow';
                        return $days . ' days';
                    }),
            ])
            ->paginated(false);
    }
}
