<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DeliverySession;
use Illuminate\Auth\Access\HandlesAuthorization;

class DeliverySessionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('delivery.manage');
    }

    public function view(User $user, DeliverySession $deliverySession): bool
    {
        return $user->can('delivery.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('delivery.manage');
    }

    public function update(User $user, DeliverySession $deliverySession): bool
    {
        return $user->can('delivery.manage');
    }

    public function delete(User $user, DeliverySession $deliverySession): bool
    {
        return $user->can('delivery.manage');
    }
}
