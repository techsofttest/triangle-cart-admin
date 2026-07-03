<?php

namespace App\Filament\Resources\DeliveryPostcodes;

use App\Filament\Resources\DeliveryPostcodes\Pages\CreateDeliveryPostcode;
use App\Filament\Resources\DeliveryPostcodes\Pages\EditDeliveryPostcode;
use App\Filament\Resources\DeliveryPostcodes\Pages\ListDeliveryPostcodes;
use App\Filament\Resources\DeliveryPostcodes\Schemas\DeliveryPostcodeForm;
use App\Filament\Resources\DeliveryPostcodes\Tables\DeliveryPostcodesTable;
use App\Models\DeliveryPostcode;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DeliveryPostcodeResource extends Resource
{
    protected static ?string $model = DeliveryPostcode::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $recordTitleAttribute = 'postcode';

    protected static string|UnitEnum|null $navigationGroup = 'Ecommerce';

    protected static ?string $modelLabel = 'Delivery Postcode';

    protected static ?string $pluralModelLabel = 'Delivery Postcodes';

    public static function form(Schema $schema): Schema
    {
        return DeliveryPostcodeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeliveryPostcodesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeliveryPostcodes::route('/'),
            'create' => CreateDeliveryPostcode::route('/create'),
            'edit' => EditDeliveryPostcode::route('/{record}/edit'),
        ];
    }
}
