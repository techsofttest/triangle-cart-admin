<?php

namespace App\Filament\Resources\HomePageSections;

use App\Filament\Resources\HomePageSections\Pages\CreateHomePageSection;
use App\Filament\Resources\HomePageSections\Pages\EditHomePageSection;
use App\Filament\Resources\HomePageSections\Pages\ListHomePageSections;
use App\Filament\Resources\HomePageSections\Schemas\HomePageSectionForm;
use App\Filament\Resources\HomePageSections\Tables\HomePageSectionsTable;
use App\Models\HomePageSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HomePageSectionResource extends Resource
{
    protected static ?string $model = HomePageSection::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Content Management';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return HomePageSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomePageSectionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHomePageSections::route('/'),
            'create' => CreateHomePageSection::route('/create'),
            'edit' => EditHomePageSection::route('/{record}/edit'),
        ];
    }
}
