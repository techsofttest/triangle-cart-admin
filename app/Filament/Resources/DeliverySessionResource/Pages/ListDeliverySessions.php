<?php

namespace App\Filament\Resources\DeliverySessionResource\Pages;

use App\Filament\Resources\DeliverySessionResource\DeliverySessionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeliverySessions extends ListRecords
{
    protected static string $resource = DeliverySessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
