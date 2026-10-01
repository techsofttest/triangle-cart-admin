<?php

namespace App\Filament\Resources\Promotions\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class PromotionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('offer_image')
                    ->label('Image'),

                TextColumn::make('name')
                    ->label('Offer Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => strtoupper(str_replace('_', ' ', $state))),

                TextColumn::make('eligible_quantity')
                    ->label('Eligible Qty')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('start_at')
                    ->label('Start Date')
                    ->dateTime('M d, Y H:i'),

                TextColumn::make('end_at')
                    ->label('End Date')
                    ->dateTime('M d, Y H:i'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
