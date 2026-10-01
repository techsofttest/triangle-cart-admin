<?php

namespace App\Filament\Resources\FreeGiftPromotions\Pages;

use App\Filament\Resources\FreeGiftPromotions\FreeGiftPromotionResource;
use App\Services\FreeGiftPromotionService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateFreeGiftPromotion extends CreateRecord
{
    protected static string $resource = FreeGiftPromotionResource::class;

    protected function beforeCreate(): void
    {
        $status = $this->data['status'] ?? 'active';
        if ($status === 'active') {
            $deactivatedCount = app(FreeGiftPromotionService::class)->deactivateOtherActivePromotions(null, true);
            if ($deactivatedCount > 0) {
                Notification::make()
                    ->title('Previous Promotion Deactivated')
                    ->body('The existing active free gift promotion was automatically set to inactive.')
                    ->warning()
                    ->send();
            }
        }
    }

    protected function afterCreate(): void
    {
        $promotion = $this->record;
        $productIds = $promotion->freeProducts()->pluck('products.id')->all();
        app(FreeGiftPromotionService::class)->syncProductFreeGiftFlags($promotion, $productIds, []);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
