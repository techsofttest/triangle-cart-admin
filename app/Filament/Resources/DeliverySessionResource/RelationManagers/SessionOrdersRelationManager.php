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
                        $service = app(\App\Services\DeliverySessionService::class);
                        $count = $service->pullOrders($session);

                        if ($count === 0) {
                            Notification::make()
                                ->title('No new paid orders found for this slot.')
                                ->info()
                                ->send();
                            return;
                        }

                        Notification::make()
                            ->title('Successfully pulled ' . $count . ' orders.')
                            ->success()
                            ->send();
                    }),

                Action::make('optimize_route')
                    ->label('Optimize Route')
                    ->color('success')
                    ->icon('heroicon-o-map')
                    ->action(function () {
                        $session = $this->getOwnerRecord();
                        $service = app(\App\Services\DeliverySessionService::class);
                        $result = $service->optimizeRoute($session);

                        if (!$result) {
                            Notification::make()
                                ->title('No orders to optimize.')
                                ->warning()
                                ->send();
                            return;
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
