<?php

namespace App\Filament\Resources\DeliverySessionResource\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use App\Models\TimeSlot;
use App\Services\DeliverySessionService;
use Carbon\Carbon;

class DeliverySessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Select::make('staff_id')
                    ->relationship('staff', 'name', function ($query) {
                        $user = auth()->user();
                        if ($user && ($user->hasRole('Staff') || $user->role === 'staff')) {
                            $query->where('id', $user->id);
                        } else {
                            $query->where('role', 'staff')
                                ->orWhereHas('roles', fn ($q) => $q->where('name', 'Staff'))
                                ->orWhereHas('permissions', fn ($q) => $q->where('name', 'delivery.driver'));
                        }
                    })
                    ->label('Staff')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $set('today_order_ids', static::getTodayDefaultIds($get));
                        $set('missed_order_ids', []);
                        $set('future_order_ids', []);
                    })
                    ->default(fn () => auth()->id())
                    ->disabled(fn () => auth()->user()?->hasRole('Staff') || auth()->user()?->role === 'staff')
                    ->dehydrated(true),

                DatePicker::make('delivery_date')
                    ->label('Delivery Date')
                    ->required()
                    ->default(Carbon::today()->toDateString())
                    ->native(false)
                    ->disabled(fn ($operation) => $operation !== 'create')
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        $defaultSlotId = TimeSlot::query()
                            ->whereHas('deliveryDate', fn ($q) => $q->whereDate('date', $state ?? Carbon::today()->toDateString()))
                            ->orderBy('start_time')
                            ->first()?->id;

                        if (! $defaultSlotId) {
                            $defaultSlotId = TimeSlot::query()->orderBy('start_time')->first()?->id;
                        }

                        $set('delivery_slot_id', $defaultSlotId);
                        $set('today_order_ids', static::getTodayDefaultIds($get));
                        $set('missed_order_ids', []);
                        $set('future_order_ids', []);
                    })
                    ->dehydrated(true),

                Select::make('delivery_slot_id')
                    ->label('Time Slot')
                    ->required()
                    ->live()
                    ->default(function (Get $get) {
                        $date = $get('delivery_date') ?? Carbon::today()->toDateString();
                        $firstSlot = TimeSlot::query()
                            ->whereHas('deliveryDate', fn ($query) => $query->whereDate('date', $date))
                            ->orderBy('start_time')
                            ->first();

                        return $firstSlot?->id ?? TimeSlot::query()->orderBy('start_time')->first()?->id;
                    })
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $set('today_order_ids', static::getTodayDefaultIds($get));
                    })
                    ->options(function (Get $get) {
                        $date = $get('delivery_date') ?? Carbon::today()->toDateString();
                        $slots = TimeSlot::query()
                            ->whereHas('deliveryDate', fn ($query) => $query->whereDate('date', $date))
                            ->orderBy('start_time')
                            ->get();

                        if ($slots->isEmpty()) {
                            $slots = TimeSlot::query()->orderBy('start_time')->get();
                        }

                        return $slots->mapWithKeys(fn ($slot) => [
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

                Section::make('ORDERS TO ADD')
                    ->schema([
                        Section::make('MISSED / PREVIOUS ORDERS')
                            ->description('Paid orders whose original delivery date has passed and were not delivered.')
                            ->schema([
                                CheckboxList::make('missed_order_ids')
                                    ->hiddenLabel()
                                    ->options(function (Get $get) {
                                        return static::getEligibleOrderOptions($get, 'missed');
                                    })
                                    ->default([])
                                    ->dehydrated(true),
                            ])
                            ->collapsible(),

                        Section::make("TODAY'S ORDERS")
                            ->description('Paid orders scheduled for this delivery date and time slot.')
                            ->schema([
                                CheckboxList::make('today_order_ids')
                                    ->hiddenLabel()
                                    ->options(function (Get $get) {
                                        return static::getEligibleOrderOptions($get, 'today');
                                    })
                                    ->default(function (Get $get) {
                                        return static::getTodayDefaultIds($get);
                                    })
                                    ->dehydrated(true),
                            ])
                            ->collapsible(),

                        Section::make('FUTURE ORDERS')
                            ->description('Paid orders scheduled for a future delivery date.')
                            ->schema([
                                CheckboxList::make('future_order_ids')
                                    ->hiddenLabel()
                                    ->options(function (Get $get) {
                                        return static::getEligibleOrderOptions($get, 'future');
                                    })
                                    ->default([])
                                    ->dehydrated(true),
                            ])
                            ->collapsible(),
                    ])
                    ->visible(fn ($operation) => $operation === 'create'),
            ]);
    }

    protected static function getEligibleOrderOptions(Get $get, string $category): array
    {
        $date = $get('delivery_date') ?? Carbon::today()->toDateString();
        $slotId = $get('delivery_slot_id') ? (int)$get('delivery_slot_id') : null;
        $staffId = $get('staff_id') ? (int)$get('staff_id') : null;

        if ($category === 'today' && ! $slotId) {
            return [];
        }

        $eligible = app(DeliverySessionService::class)->getEligibleOrders($date, $slotId, $staffId);
        $collection = $eligible[$category] ?? collect();

        return $collection->mapWithKeys(function ($o) {
            $customer = $o->customer_name ?: trim(($o->first_name ?? '') . ' ' . ($o->last_name ?? ''));
            if (empty($customer)) {
                $customer = $o->email ?? 'Guest';
            }
            $dateStr = Carbon::parse($o->delivery_date)->format('M d, Y');
            return [
                $o->id => "#{$o->order_number} — {$customer} (Delivery: {$dateStr})",
            ];
        })->toArray();
    }

    protected static function getTodayDefaultIds(Get $get): array
    {
        $date = $get('delivery_date') ?? Carbon::today()->toDateString();
        $slotId = $get('delivery_slot_id') ? (int)$get('delivery_slot_id') : null;
        $staffId = $get('staff_id') ? (int)$get('staff_id') : null;

        if (! $slotId) {
            return [];
        }

        $eligible = app(DeliverySessionService::class)->getEligibleOrders($date, $slotId, $staffId);

        return $eligible['today']->pluck('id')->toArray();
    }
}
