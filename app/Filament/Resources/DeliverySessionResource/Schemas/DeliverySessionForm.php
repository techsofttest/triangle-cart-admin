<?php

namespace App\Filament\Resources\DeliverySessionResource\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use App\Models\TimeSlot;
use Carbon\Carbon;


class DeliverySessionForm
{
    public static function configure(Schema $schema): Schema
    { 
        return $schema
                    ->columns(1)
                    ->components([
                      DatePicker::make('delivery_date')
                    ->label('Delivery Date')
                    ->required()
                    ->default(Carbon::today()->toDateString())
                    ->native(false)
                    ->disabled()
                    ->dehydrated(false),
    
                    Select::make('delivery_slot_id')
                    ->label('Time Slot')
                    ->required()
                    ->options(function () {
                        return TimeSlot::query()
                            ->whereHas('deliveryDate', fn ($query) => $query->whereDate('date', today()))
                            ->orderBy('start_time')
                            ->get()
                            ->mapWithKeys(fn ($slot) => [
                                $slot->id => Carbon::parse($slot->start_time)->format('g:i A')
                                    . ' - '
                                    . Carbon::parse($slot->end_time)->format('g:i A'),
                            ]);
                    }),
                         
                        Select::make('status')
                            ->required()
                            ->options([
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                            ])
                            ->default('in_progress'),
                        
                            DateTimePicker::make('started_at')
                                ->disabled()
                                ->dehydrated(false),
                            DateTimePicker::make('completed_at')
                                ->disabled()
                                ->dehydrated(false),
                           
                    
            ]);
    }
}
