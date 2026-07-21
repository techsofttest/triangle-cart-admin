<?php

namespace App\Filament\Resources\DeliverySessionResource\Pages;

use App\Filament\Resources\DeliverySessionResource\DeliverySessionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewDeliverySession extends ViewRecord
{
    protected static string $resource = DeliverySessionResource::class;

    protected string $view = 'filament.resources.delivery-sessions.pages.delivery-session-detail';

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
