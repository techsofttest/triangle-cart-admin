<?php

namespace App\Filament\Resources\FreeGiftPromotions\Pages;

use App\Filament\Resources\FreeGiftPromotions\FreeGiftPromotionResource;
use App\Services\FreeGiftPromotionService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditFreeGiftPromotion extends EditRecord
{
    protected static string $resource = FreeGiftPromotionResource::class;

    protected array $previousProductIds = [];

    protected function beforeSave(): void
    {
        $this->previousProductIds = $this->record->freeProducts()->pluck('products.id')->all();

        $status = $this->data['status'] ?? 'active';
        if ($status === 'active') {
            $deactivatedCount = app(FreeGiftPromotionService::class)->deactivateOtherActivePromotions($this->record->id, true);
            if ($deactivatedCount > 0) {
                Notification::make()
                    ->title('Previous Promotion Deactivated')
                    ->body('The existing active free gift promotion was automatically set to inactive.')
                    ->warning()
                    ->send();
            }
        }
    }

    protected function afterSave(): void
    {
        $promotion = $this->record;
        $currentProductIds = $promotion->freeProducts()->pluck('products.id')->all();
        app(FreeGiftPromotionService::class)->syncProductFreeGiftFlags($promotion, $currentProductIds, $this->previousProductIds);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(function () {
                    $promotion = $this->record;
                    $productIds = $promotion->freeProducts()->pluck('products.id')->all();
                    app(FreeGiftPromotionService::class)->syncProductFreeGiftFlags($promotion, [], $productIds);
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
