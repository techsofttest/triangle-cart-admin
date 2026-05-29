<?php

namespace App\Filament\Resources\DeliveryDates\Pages;

use App\Filament\Resources\DeliveryDates\DeliveryDateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryDate extends EditRecord
{
    protected static string $resource = DeliveryDateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
