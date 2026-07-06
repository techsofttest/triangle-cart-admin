<?php

namespace App\Filament\Resources\DeliverySessionResource\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Grid;
use App\Models\TimeSlot;
use Carbon\Carbon;

class DeliverySessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Grid::make(2)
                    ->schema([
                        DatePicker::make('delivery_date')
                            ->label('Delivery Date')
                            ->required()
                            ->default(now()),
                            
                        Select::make('delivery_slot_id')
                            ->label('Time Slot')
                            ->required()
                            ->options(function () {
                                return TimeSlot::with('deliveryDate')->get()->mapWithKeys(function ($slot) {
                                    $dateStr = $slot->deliveryDate ? Carbon::parse($slot->deliveryDate->date)->format('Y-m-d') : 'No Date';
                                    return [$slot->id => "{$dateStr} : {$slot->start_time} - {$slot->end_time}"];
                                });
                            }),
                            
                        Select::make('status')
                            ->required()
                            ->options([
                                'draft' => 'Draft',
                                'preparing' => 'Preparing',
                                'ready' => 'Ready',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                            ])
                            ->default('draft'),

                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('started_at')
                                    ->disabled()
                                    ->dehydrated(false),
                                DateTimePicker::make('completed_at')
                                    ->disabled()
                                    ->dehydrated(false),
                            ])
                            ->columnSpan(2),
                    ])
            ]);
    }
}
