<?php

namespace App\Filament\Resources\Advertisements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class AdvertisementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                ImageColumn::make('banner')
                    ->disk('public'),
                TextColumn::make('location')
                    ->searchable(),
                TextColumn::make('url')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // No delete bulk action
                ]),
            ]);
    }
}
