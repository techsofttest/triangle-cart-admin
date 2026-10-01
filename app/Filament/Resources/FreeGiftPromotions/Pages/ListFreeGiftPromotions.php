<?php

namespace App\Filament\Resources\FreeGiftPromotions\Pages;

use App\Filament\Resources\FreeGiftPromotions\FreeGiftPromotionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFreeGiftPromotions extends ListRecords
{
    protected static string $resource = FreeGiftPromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
