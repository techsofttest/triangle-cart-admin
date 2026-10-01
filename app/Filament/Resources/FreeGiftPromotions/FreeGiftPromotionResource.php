<?php

namespace App\Filament\Resources\FreeGiftPromotions;

use App\Filament\Resources\FreeGiftPromotions\Pages\CreateFreeGiftPromotion;
use App\Filament\Resources\FreeGiftPromotions\Pages\EditFreeGiftPromotion;
use App\Filament\Resources\FreeGiftPromotions\Pages\ListFreeGiftPromotions;
use App\Filament\Resources\FreeGiftPromotions\Schemas\FreeGiftPromotionForm;
use App\Filament\Resources\FreeGiftPromotions\Tables\FreeGiftPromotionsTable;
use App\Models\FreeGiftPromotion;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FreeGiftPromotionResource extends Resource
{
    protected static ?string $model = FreeGiftPromotion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGift;

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Free Gifts';

    public static function form(Schema $schema): Schema
    {
        return FreeGiftPromotionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FreeGiftPromotionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFreeGiftPromotions::route('/'),
            'create' => CreateFreeGiftPromotion::route('/create'),
            'edit' => EditFreeGiftPromotion::route('/{record}/edit'),
        ];
    }
}
