<?php

namespace App\Filament\Resources\FreeGiftPromotions\Tables;

use App\Models\FreeGiftPromotion;
use App\Services\FreeGiftPromotionService;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class FreeGiftPromotionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Promotion Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('minimum_cart_amount')
                    ->label('Min Cart')
                    ->money('AUD')
                    ->sortable(),

                TextColumn::make('free_products_count')
                    ->label('Gift Options')
                    ->counts('freeProducts')
                    ->sortable(),

                TextColumn::make('starts_at')
                    ->label('Start Date')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('ends_at')
                    ->label('End Date')
                    ->dateTime()
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->afterStateUpdated(function ($livewire, FreeGiftPromotion $record, bool $state) {
                        if ($state) {
                            $deactivatedCount = app(FreeGiftPromotionService::class)->deactivateOtherActivePromotions($record->id, true);
                            if ($deactivatedCount > 0) {
                                Notification::make()
                                    ->title('Other Promotions Deactivated')
                                    ->body("Promotion '{$record->name}' is now active. All other free gift promotions have been set to inactive.")
                                    ->warning()
                                    ->send();
                            }
                        }
                        if ($livewire && method_exists($livewire, 'resetTable')) {
                            $livewire->resetTable();
                        }
                    }),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
