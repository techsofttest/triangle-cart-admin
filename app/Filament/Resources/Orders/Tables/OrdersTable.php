<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id','desc')
            ->columns([
                TextColumn::make('order_number')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assignedStaff.name')
                    ->label('Staff')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->label('C-Name')
                    ->searchable(),
                TextColumn::make('delivery_type')
                    ->label('Del Type')
                    ->badge()
                    ->color(fn ($state): string => $state === 'direct' ? 'success' : 'gray'),
                TextColumn::make('delivery_slot_info')
                    ->label('Slot')
                    ->state(function ($record): ?string {
                        if ($record->delivery_type !== 'direct') {
                            return null;
                        }
                        $parts = [];
                        if ($record->delivery_date) {
                            $parts[] = \Carbon\Carbon::parse($record->delivery_date)->format('d M Y');
                        }
                        if ($record->deliverySlot) {
                            $parts[] =
                            \Carbon\Carbon::parse($record->deliverySlot->start_time)->format('g:i A')
                            . ' - ' .
                            \Carbon\Carbon::parse($record->deliverySlot->end_time)->format('g:i A');
                        }
                        return implode(' | ', $parts) ?: null;
                    })
                    ->placeholder('-'),
                TextColumn::make('shipping_postcode')
                    ->label('Postcode')
                    ->searchable(),
                TextColumn::make('deliveryPostcode.region')
                    ->label('Region')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('grand_total')
                    ->label('Total')
                    ->money('AUD')
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(function ($state): string {
                        $value = $state instanceof \BackedEnum ? $state->value : $state;
                        return match ($value) {
                            'pending' => 'warning',
                            'paid' => 'success',
                            'failed' => 'danger',
                            default => 'gray',
                        };
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(function ($state): string {
                        $value = $state instanceof \BackedEnum ? $state->value : $state;
                        return match ($value) {
                            'pending', 'pending_payment' => 'warning',
                            'confirmed' => 'success',
                            'processing' => 'info',
                            'packed' => 'primary',
                            'ready' => 'info',
                            'out_for_delivery' => 'primary',
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                            default => 'gray',
                        };
                    }),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Payment Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('assigned_staff_id')
                    ->label('Assigned Staff')
                    ->options(function () {
                        return \App\Models\User::where('role', 'staff')
                            ->orWhereHas('roles', fn ($q) => $q->where('name', 'Staff'))
                            ->orWhereHas('permissions', fn ($q) => $q->where('name', 'delivery.driver'))
                            ->pluck('name', 'id');
                    }),
                \Filament\Tables\Filters\Filter::make('unassigned')
                    ->label('Unassigned Orders')
                    ->query(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->whereNull('assigned_staff_id')),
                \Filament\Tables\Filters\Filter::make('from_date')
                    ->label('From Date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from_date')
                            ->label('From Date')
                            ->placeholder('Select start date'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): void {
                        if (isset($data['from_date'])) {
                            $query->whereDate('created_at', '>=', $data['from_date']);
                        }
                    }),
                \Filament\Tables\Filters\Filter::make('to_date')
                    ->label('To Date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('to_date')
                            ->label('To Date')
                            ->placeholder('Select end date'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): void {
                        if (isset($data['to_date'])) {
                            $query->whereDate('created_at', '<=', $data['to_date']);
                        }
                    }),
                \Filament\Tables\Filters\SelectFilter::make('delivery_slot_id')
                    ->label('Delivery Slot')
                    ->options(function () {
                        return \App\Models\TimeSlot::with('deliveryDate')
                            ->get()
                            ->mapWithKeys(function ($slot) {
                                $date = $slot->deliveryDate?->date ?? '';
                                $timeRange = \Carbon\Carbon::parse($slot->start_time)->format('g:i A') . ' - ' . \Carbon\Carbon::parse($slot->end_time)->format('g:i A');
                                $label = $date ? "{$date} ({$timeRange})" : $timeRange;
                                return [$slot->id => $label];
                            })
                            ->toArray();
                    })
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                \Filament\Actions\Action::make('assignStaff')
                    ->label('Assign Staff')
                    ->icon('heroicon-o-user')
                    ->visible(fn () => auth()->user()?->can('orders.assign') ?? false)
                    ->form([
                        \Filament\Forms\Components\Select::make('assigned_staff_id')
                            ->label('Staff Member')
                            ->options(function () {
                                return \App\Models\User::where('role', 'staff')
                                    ->orWhereHas('roles', fn ($q) => $q->where('name', 'Staff'))
                                    ->orWhereHas('permissions', fn ($q) => $q->where('name', 'delivery.driver'))
                                    ->pluck('name', 'id');
                            })
                            ->placeholder('Select a staff member')
                            ->required(),
                    ])
                    ->action(function (\App\Models\Order $record, array $data): void {
                        $record->update([
                            'assigned_staff_id' => $data['assigned_staff_id'],
                            'assigned_at' => now(),
                            'assigned_by' => auth()->id(),
                        ]);
                    }),
            ])
            ->toolbarActions([
                \Filament\Actions\Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($livewire) {
                        $query = $livewire->getFilteredTableQuery();

                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\OrdersExport($query),
                            'orders-' . now()->format('Y-m-d') . '.xlsx'
                        );
                    }),
                BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('bulkAssignStaff')
                        ->label('Assign Staff')
                        ->icon('heroicon-o-user')
                        ->visible(fn () => auth()->user()?->can('orders.assign') ?? false)
                        ->form([
                            \Filament\Forms\Components\Select::make('assigned_staff_id')
                                ->label('Staff Member')
                                ->options(function () {
                                    return \App\Models\User::where('role', 'staff')
                                        ->orWhereHas('roles', fn ($q) => $q->where('name', 'Staff'))
                                        ->orWhereHas('permissions', fn ($q) => $q->where('name', 'delivery.driver'))
                                        ->pluck('name', 'id');
                                })
                                ->placeholder('Select a staff member')
                                ->required(),
                        ])
                        ->action(function (\Illuminate\Support\Collection $records, array $data): void {
                            $records->each(function ($record) use ($data) {
                                $record->update([
                                    'assigned_staff_id' => $data['assigned_staff_id'],
                                    'assigned_at' => now(),
                                    'assigned_by' => auth()->id(),
                                ]);
                            });
                        }),
                ]),
            ]);
    }
}
