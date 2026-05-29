<?php

namespace App\Filament\Resources\Coupons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
   use Filament\Tables\Columns\BadgeColumn;
   
class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('coupon_code')
                    ->searchable(),
                TextColumn::make('coupon_amount')
                    ->numeric()
                    ->sortable(),
            

BadgeColumn::make('coupon_type')
    ->label('Type')
    ->formatStateUsing(fn ($state) => $state == 0 ? 'Cash' : 'Percentage')
    ->colors([
        'success' => fn ($state) => $state == 0,
        'warning' => fn ($state) => $state == 1,
    ]),
                TextColumn::make('coupon_fromdate')
                    ->date()
                    ->sortable() 
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('coupon_todate')
                    ->date()
                    ->sortable() 
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
