<?php

namespace App\Filament\Resources\DeliveryDates;

use App\Filament\Resources\DeliveryDates\Pages\CreateDeliveryDate;
use App\Filament\Resources\DeliveryDates\Pages\EditDeliveryDate;
use App\Filament\Resources\DeliveryDates\Pages\ListDeliveryDates;
use App\Filament\Resources\DeliveryDates\Schemas\DeliveryDateForm;
use App\Filament\Resources\DeliveryDates\Tables\DeliveryDatesTable;
use App\Models\DeliveryDate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DeliveryDateResource extends Resource
{
    protected static ?string $model = DeliveryDate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $modelLabel = 'Slot';
    protected static ?string $pluralModelLabel = 'Slot Management';
    protected static ?string $navigationLabel = 'Slot Management';
    protected static string|UnitEnum|null $navigationGroup = 'Ecommerce';

    public static function form(Schema $schema): Schema
    {
        return DeliveryDateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeliveryDatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeliveryDates::route('/'),
            'create' => CreateDeliveryDate::route('/create'),
            'edit' => EditDeliveryDate::route('/{record}/edit'),
        ];
    }
}
