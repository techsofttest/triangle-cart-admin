<?php

namespace App\Filament\Resources\DeliveryPostcodes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DeliveryPostcodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('postcode')
                    ->searchable()
                    ->sortable(),
                /*TextColumn::make('warehouse_id')
                    ->label('Warehouse')
                    ->sortable(),
                TextColumn::make('delivery_fee')
                    ->money('INR')
                    ->sortable(),
                TextColumn::make('free_shipping_threshold')
                    ->money('INR')
                    ->sortable(),*/
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
