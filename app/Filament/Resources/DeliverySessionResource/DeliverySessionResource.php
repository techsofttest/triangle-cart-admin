<?php

namespace App\Filament\Resources\DeliverySessionResource;

use App\Filament\Resources\DeliverySessionResource\Pages\CreateDeliverySession;
use App\Filament\Resources\DeliverySessionResource\Pages\EditDeliverySession;
use App\Filament\Resources\DeliverySessionResource\Pages\ListDeliverySessions;
use App\Filament\Resources\DeliverySessionResource\Pages\ViewDeliverySession;
use App\Filament\Resources\DeliverySessionResource\Schemas\DeliverySessionForm;
use App\Filament\Resources\DeliverySessionResource\Tables\DeliverySessionTable;
use App\Filament\Resources\DeliverySessionResource\RelationManagers\SessionOrdersRelationManager;
use App\Models\DeliverySession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class DeliverySessionResource extends Resource
{
    protected static ?string $model = DeliverySession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $recordTitleAttribute = 'delivery_date';

    protected static string|UnitEnum|null $navigationGroup = 'Delivery';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return DeliverySessionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeliverySessionTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SessionOrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeliverySessions::route('/'),
            'create' => CreateDeliverySession::route('/create'),
            'view' => ViewDeliverySession::route('/{record}'),
            'edit' => EditDeliverySession::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();
        
        if ($user && ($user->hasRole('Staff') || $user->role === 'staff')) {
            $query->where('staff_id', $user->id);
        }
        
        return $query;
    }
}
