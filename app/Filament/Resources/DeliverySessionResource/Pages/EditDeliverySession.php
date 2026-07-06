<?php

namespace App\Filament\Resources\DeliverySessionResource\Pages;

use App\Filament\Resources\DeliverySessionResource\DeliverySessionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use App\Enums\OrderStatus;

class EditDeliverySession extends EditRecord
{
    protected static string $resource = DeliverySessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('start_session')
                ->label('Start Session')
                ->color('success')
                ->icon('heroicon-o-play')
                ->visible(fn () => in_array($this->record->status, ['draft', 'preparing', 'ready']))
                ->action(function () {
                    $this->record->update([
                        'status' => 'in_progress',
                        'started_at' => now(),
                    ]);

                    // Update order statuses
                    foreach ($this->record->sessionOrders as $sessionOrder) {
                        $order = $sessionOrder->order;
                        if ($order) {
                            $order->update([
                                'status' => OrderStatus::OUT_FOR_DELIVERY,
                            ]);
                        }
                    }

                    Notification::make()
                        ->title('Delivery Session started successfully!')
                        ->success()
                        ->send();
                }),

            Action::make('complete_session')
                ->label('Complete Session')
                ->color('info')
                ->icon('heroicon-o-check-circle')
                ->visible(fn () => $this->record->status === 'in_progress')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Delivery Session completed!')
                        ->success()
                        ->send();
                }),

            DeleteAction::make(),
        ];
    }
}
