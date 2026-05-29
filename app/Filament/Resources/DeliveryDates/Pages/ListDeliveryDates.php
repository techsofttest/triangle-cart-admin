<?php

namespace App\Filament\Resources\DeliveryDates\Pages;

use App\Filament\Resources\DeliveryDates\DeliveryDateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryDates extends ListRecords
{
    protected static string $resource = DeliveryDateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
