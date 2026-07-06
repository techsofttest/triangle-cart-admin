<?php

namespace App\Filament\Resources\DeliveryDates\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TimePicker;

class DeliveryDateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->label('Delivery Date')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->minDate(today())
                    ->columnSpanFull(),
                
                Repeater::make('timeSlots')
                    ->relationship()
                    ->schema([
                        TimePicker::make('start_time')
                            ->required()
                            ->seconds(false),
                        TimePicker::make('end_time')
                            ->required()
                            ->seconds(false)
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->columnSpanFull()
                    ->itemLabel(fn (array $state): ?string => ($state['start_time'] ?? '') . ' - ' . ($state['end_time'] ?? '')),
            ]);
    }
}
