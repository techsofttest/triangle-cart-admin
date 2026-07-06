<?php

namespace App\Filament\Resources\DeliverySessionResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use App\Models\Order;
use App\Models\DeliverySessionOrder;
use App\Enums\PaymentStatus;
use App\Enums\OrderStatus;
use App\Services\GoogleRoutesService;

class SessionOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'sessionOrders';

    protected static ?string $title = 'Session Orders';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('stop_sequence')
                    ->label('Stop Seq')
                    ->sortable(),

                TextColumn::make('order.order_number')
                    ->label('Order #')
                    ->searchable(),

                TextColumn::make('order.customer_name')
                    ->label('Customer')
                    ->searchable(),

                TextColumn::make('order.shipping_address_line_1')
                    ->label('Address')
                    ->limit(30),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'delivered' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('eta')
                    ->label('ETA'),

                TextColumn::make('delivered_at')
                    ->dateTime()
                    ->label('Delivered At'),
            ])
            ->headerActions([
                Action::make('pull_orders')
                    ->label('Pull Slot Orders')
                    ->color('primary')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        $session = $this->getOwnerRecord();
                        
                        // Pull orders matching delivery_date, delivery_slot_id, paid status
                        $orders = Order::where('delivery_date', $session->delivery_date)
                            ->where('delivery_slot_id', $session->delivery_slot_id)
                            ->where('payment_status', PaymentStatus::PAID)
                            ->whereNotIn('id', function ($query) {
                                $query->select('order_id')->from('delivery_session_orders');
                            })
                            ->get();

                        if ($orders->isEmpty()) {
                            Notification::make()
                                ->title('No new paid orders found for this slot.')
                                ->info()
                                ->send();
                            return;
                        }

                        $seq = DeliverySessionOrder::where('delivery_session_id', $session->id)->max('stop_sequence') ?? 0;
                        
                        foreach ($orders as $order) {
                            $session->sessionOrders()->create([
                                'order_id' => $order->id,
                                'stop_sequence' => ++$seq,
                                'status' => 'pending',
                            ]);
                        }

                        Notification::make()
                            ->title('Successfully pulled ' . $orders->count() . ' orders.')
                            ->success()
                            ->send();
                    }),

                Action::make('optimize_route')
                    ->label('Optimize Route')
                    ->color('success')
                    ->icon('heroicon-o-map')
                    ->action(function () {
                        $session = $this->getOwnerRecord();
                        $orders = $session->sessionOrders()->with('order')->get();
                        
                        if ($orders->isEmpty()) {
                            Notification::make()
                                ->title('No orders to optimize.')
                                ->warning()
                                ->send();
                            return;
                        }

                        $destinations = [];
                        foreach ($orders as $sessionOrder) {
                            $order = $sessionOrder->order;
                            // Make sure we have lat/lng
                            $lat = $order->shipping_latitude;
                            $lng = $order->shipping_longitude;
                            
                            // Fallback if not set: try to parse suburb or use default
                            if (!$lat || !$lng) {
                                $lat = config('delivery.store_coordinates.latitude', -37.8136) + (rand(-50, 50) / 1000);
                                $lng = config('delivery.store_coordinates.longitude', 144.9631) + (rand(-50, 50) / 1000);
                            }

                            $destinations[] = [
                                'id' => $sessionOrder->id,
                                'lat' => (float)$lat,
                                'lng' => (float)$lng,
                            ];
                        }

                        $origin = [
                            'lat' => (float)config('delivery.store_coordinates.latitude', -37.8136),
                            'lng' => (float)config('delivery.store_coordinates.longitude', 144.9631),
                        ];

                        $routesService = app(GoogleRoutesService::class);
                        $optimized = $routesService->optimizeRoute($origin, $destinations);

                        foreach ($optimized as $opt) {
                            DeliverySessionOrder::where('id', $opt['id'])->update([
                                'stop_sequence' => $opt['stop_sequence'],
                                'eta' => $opt['eta'],
                            ]);
                        }

                        Notification::make()
                            ->title('Route optimization completed!')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        TextInput::make('stop_sequence')
                            ->numeric()
                            ->required(),
                        TextInput::make('eta')
                            ->label('ETA'),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'delivered' => 'Delivered',
                                'failed' => 'Failed',
                            ])
                            ->required(),
                        TextInput::make('failure_reason')
                            ->label('Failure Reason'),
                    ]),
                DeleteAction::make()
                    ->label('Remove'),
            ]);
    }
}
