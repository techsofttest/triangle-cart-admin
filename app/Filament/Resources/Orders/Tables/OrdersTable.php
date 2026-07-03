<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id','desc')
            ->columns([
                TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->searchable(),
                TextColumn::make('delivery_type')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'direct' ? 'success' : 'gray'),
                TextColumn::make('shipping_postcode')
                    ->label('Postcode')
                    ->searchable(),
                TextColumn::make('grand_total')
                    ->money('INR')
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->badge()
                    ->color(function (string $state): string {
                        return match ($state) {
                            'pending' => 'warning',
                            'paid' => 'success',
                            'failed' => 'danger',
                            default => 'gray',
                        };
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(function (string $state): string {
                        return match ($state) {
                            'pending' => 'warning',
                            'processing' => 'info',
                            'shipped' => 'primary',
                            'delivered', 'completed' => 'success',
                            'cancelled' => 'danger',
                            default => 'gray',
                        };
                    }),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                //EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //DeleteBulkAction::make(),
                ]),
            ]);
    }
}
