<?php

namespace App\Filament\Resources\DeliverySessionResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class DeliverySessionTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('delivery_date')
                    ->date()
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('timeSlot')
                    ->label('Time Slot')
                    ->formatStateUsing(function ($record) {
                        if (!$record->timeSlot) return '';
                        return "{$record->timeSlot->start_time} - {$record->timeSlot->end_time}";
                    }),

                TextColumn::make('staff.name')
                    ->label('Staff')
                    ->placeholder('-')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'preparing' => 'warning',
                        'ready' => 'info',
                        'in_progress' => 'primary',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                
                TextColumn::make('session_orders_count')
                    ->counts('sessionOrders')
                    ->label('Orders Count')
                    ->badge(),
                
                TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
