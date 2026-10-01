<?php

namespace App\Filament\Resources\DeliverySessionResource\Pages;

use App\Filament\Resources\DeliverySessionResource\DeliverySessionResource;
use App\Services\DeliverySessionService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CreateDeliverySession extends CreateRecord
{
    protected static string $resource = DeliverySessionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $missedOrderIds = $data['missed_order_ids'] ?? [];
        $todayOrderIds = $data['today_order_ids'] ?? [];
        $futureOrderIds = $data['future_order_ids'] ?? [];

        unset($data['missed_order_ids'], $data['today_order_ids'], $data['future_order_ids']);

        $selectedOrderIds = array_merge($missedOrderIds, $todayOrderIds, $futureOrderIds);
        $selectedOrderIds = array_values(array_unique(array_filter($selectedOrderIds)));

        if (empty($selectedOrderIds)) {
            Notification::make()
                ->danger()
                ->title('No Orders Selected')
                ->body('Please select at least one order to create a delivery session.')
                ->send();

            $this->halt();
        }

        $data['status'] = $data['status'] ?? 'in_progress';

        return app(DeliverySessionService::class)->createSessionWithOrders($data, $selectedOrderIds);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
