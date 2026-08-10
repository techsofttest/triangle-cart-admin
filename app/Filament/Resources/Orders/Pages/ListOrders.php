<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use App\Models\Order;
use App\Enums\PaymentStatus;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make(),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'active';
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make('Active Orders')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('payment_status', PaymentStatus::PAID)
                    ->whereNotIn('status', [OrderStatus::DELIVERED, OrderStatus::OUT_FOR_DELIVERY])
                )
                ->badge(Order::query()
                    ->where('payment_status', PaymentStatus::PAID)
                    ->whereNotIn('status', [OrderStatus::DELIVERED, OrderStatus::OUT_FOR_DELIVERY])
                    ->count()
                ),
            'delivered' => Tab::make('Delivered')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('status', OrderStatus::DELIVERED)
                )
                ->badge(Order::query()
                    ->where('status', OrderStatus::DELIVERED)
                    ->count()
                ),
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('payment_status', PaymentStatus::PENDING)
                )
                ->badge(Order::query()
                    ->where('payment_status', PaymentStatus::PENDING)
                    ->count()
                ),
            'all' => Tab::make('All Orders')
                ->badge(Order::query()->count()),
        ];
    }
}
