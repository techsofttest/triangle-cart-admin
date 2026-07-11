<?php

namespace App\Filament\Resources\DeliverySessionResource\Pages;

use App\Filament\Resources\DeliverySessionResource\DeliverySessionResource;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;

class CreateDeliverySession extends CreateRecord
{
    protected static string $resource = DeliverySessionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['delivery_date'] = Carbon::today()->toDateString();
        $data['status'] = $data['status'] ?? 'in_progress';

        return $data;
    }
}
