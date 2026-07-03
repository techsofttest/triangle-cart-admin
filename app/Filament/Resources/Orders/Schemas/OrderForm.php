<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Summary')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('order_number')
                                ->required(),
                            TextInput::make('status')
                                ->required(),
                            Select::make('payment_status')
                                ->options([
                                    'pending' => 'Pending',
                                    'paid' => 'Paid',
                                    'failed' => 'Failed',
                                ])
                                ->required()
                                ->default('pending'),
                            TextInput::make('payment_method')
                                ->required(),
                        ]),
                    ]),

                Section::make('Customer')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('customer_id')
                                ->numeric()
                                ->disabled(),
                            TextInput::make('customer_name')
                                ->required(),
                            TextInput::make('customer_email')
                                ->email(),
                            TextInput::make('customer_phone')
                                ->tel(),
                        ]),
                    ]),

                Section::make('Shipping Snapshot')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('shipping_name'),
                            TextInput::make('shipping_phone'),
                            TextInput::make('shipping_address_line_1'),
                            TextInput::make('shipping_address_line_2'),
                            TextInput::make('shipping_suburb'),
                            TextInput::make('shipping_city'),
                            TextInput::make('shipping_state'),
                            TextInput::make('shipping_postcode'),
                            TextInput::make('shipping_country'),
                            TextInput::make('shipping_latitude'),
                            TextInput::make('shipping_longitude'),
                            TextInput::make('shipping_google_place_id'),
                            TextInput::make('delivery_type'),
                            TextInput::make('warehouse_id'),
                            TextInput::make('delivery_slot_id'),
                            TextInput::make('delivery_date'),
                            TextInput::make('delivery_distance_km'),
                        ]),
                    ]),

                Section::make('Totals')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('subtotal')->numeric()->default(0.0),
                            TextInput::make('shipping_cost')->numeric()->default(0.0)->prefix('$'),
                            TextInput::make('discount')->numeric()->default(0.0),
                            TextInput::make('coupon_code'),
                            TextInput::make('grand_total')->numeric()->default(0.0),
                        ]),
                        Textarea::make('notes')->columnSpanFull(),
                    ]),
            ]);
    }
}
