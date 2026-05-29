<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Radio;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Radio::make('coupon_type')
                    ->label('Coupon Type')
                    ->options([
                        0 => 'Cash',
                        1 => 'Percentage',
                    ])
                    ->required()
                    ->columnSpanFull()
                    ->inline(), // optional (shows horizontally)

                TextInput::make('coupon_code')
                    ->required(),

                TextInput::make('coupon_amount')
                    ->required()
                    ->numeric(),

                DatePicker::make('coupon_fromdate'),

                DatePicker::make('coupon_todate'),
            ]);
    }
}
