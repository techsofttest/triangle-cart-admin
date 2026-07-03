<?php

namespace App\Filament\Resources\DeliveryPostcodes\Schemas;

use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class DeliveryPostcodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            
            Section::make('Delivery Postcode')
                ->schema([
                    
                        TextInput::make('postcode')
                            ->label('Postcode')
                            ->required()
                            ->maxLength(20),

                        TextInput::make('warehouse_id')
                            ->label('Warehouse ID')
                            ->numeric()
                            ->nullable(),

                        TextInput::make('delivery_fee')
                            ->label('Delivery Fee')
                            ->numeric()
                            ->default(0)
                            ->prefix('₹'),

                        TextInput::make('free_shipping_threshold')
                            ->label('Free Shipping Threshold')
                            ->numeric()
                            ->nullable()
                            ->prefix('₹'),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    
                ])->columnspanfull(),
        ]);
    }
}
