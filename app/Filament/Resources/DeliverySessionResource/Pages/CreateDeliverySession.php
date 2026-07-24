<?php

namespace App\Filament\Resources\DeliverySessionResource\Pages;

use App\Filament\Resources\DeliverySessionResource\DeliverySessionResource;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateDeliverySession extends CreateRecord
{
    protected static string $resource = DeliverySessionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['delivery_date'] = Carbon::today()->toDateString();
        $data['status'] = $data['status'] ?? 'in_progress';

        // Check if there are any orders for the selected timeslot
        $ordersCount = Order::query()
            ->where('delivery_slot_id', $data['delivery_slot_id'])
            ->whereDate('delivery_date', $data['delivery_date'])
            ->count();

        if ($ordersCount === 0) {
            Notification::make()
                ->danger()
                ->title('No Orders in Timeslot')
                ->body('There are no orders scheduled for the selected timeslot. Please select a different time slot or date.')
                ->send();

            throw new \Exception('No orders found for the selected timeslot.');
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
