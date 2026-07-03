<?php

namespace App\Filament\Resources\DeliveryPostcodes\Pages;

use App\Filament\Resources\DeliveryPostcodes\DeliveryPostcodeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryPostcodes extends ListRecords
{
    protected static string $resource = DeliveryPostcodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
