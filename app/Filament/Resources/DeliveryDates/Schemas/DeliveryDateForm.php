<?php

namespace App\Filament\Resources\DeliveryDates\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;

class DeliveryDateForm
{
    public static function configure(Schema $schema): Schema
    {

                    
        $options = [];

        for ($hour = 8; $hour <= 23; $hour++) {
            $value = sprintf('%02d:00:00', $hour);
            $label = \Carbon\Carbon::createFromTime($hour)->format('g:i A');

            $options[$value] = $label;
        }



        return $schema
            ->components([
                DatePicker::make('date')
                    ->label('Delivery Date')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->minDate(today())
                    ->native(false)
                    ->columnSpanFull(),

                
                Repeater::make('timeSlots')
                    ->relationship()
                    ->schema([

                        Select::make('start_time')
                        ->options($options)
                         ->live()
                        ->required(),
                            
                        Select::make('end_time')
                        ->options(fn (Get $get) => collect($options)
                        ->filter(fn ($label, $value) => $value > $get('start_time'))
                        ->all())
                        ->live()
                        ->required()
                    ])

                    ->columns(2)
                    ->collapsible()
                    ->columnSpanFull()
                    ->itemLabel(fn (array $state): ?string => ($state['start_time'] ?? '') . ' - ' . ($state['end_time'] ?? '')),
            ]);
    }
}
