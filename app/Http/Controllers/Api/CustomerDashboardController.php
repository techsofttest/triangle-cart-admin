<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    private function getAuthenticatedCustomer(Request $request): ?Customer
    {
        $user = Auth::guard('customer')->user();

        return $user instanceof Customer ? $user : null;
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->getAuthenticatedCustomer($request);

        if (!($user instanceof Customer)) {
            return response()->json(['id' => null], 401);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'profile_image' => $user->profile_image ? asset('storage/' . $user->profile_image) : null,
        ]);
    }

    public function dashboardSummary(Request $request): JsonResponse
    {
        $user = $this->getAuthenticatedCustomer($request);

        if (!($user instanceof Customer)) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Adjust these status values based on how you set them in UI/admin.
        $activeStatuses = ['processing', 'shipped', 'out_for_delivery', 'delivered'];
        $activeStatuses = array_map(fn ($s) => strtolower($s), $activeStatuses);

        $totalOrders = Order::query()->where('user_id', $user->id)->count();

        $activeOrders = Order::query()
            ->where('user_id', $user->id)
            ->whereIn('status', $activeStatuses)
            ->count();

        $savedAddressesCount = CustomerAddress::query()
            ->where('customer_id', $user->id)
            ->count();

        $last5Orders = Order::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'order_number', 'created_at', 'status', 'grand_total']);

        return response()->json([
            'total_orders' => $totalOrders,
            'active_orders' => $activeOrders,
            'saved_addresses_count' => $savedAddressesCount,

            // Placeholders for Phase 1 UI.
            'wishlist_count' => 0,
            'reward_points' => 0,

            'last_5_orders' => $last5Orders->map(fn (Order $o) => [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'order_date' => optional($o->created_at)->toDateString(),
                'status' => $o->status,
                'grand_total' => (float) $o->grand_total,
            ])->values(),
        ]);
    }
}

