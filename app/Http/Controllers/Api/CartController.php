<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BuyXGetXPromotionService;
use App\Services\FreeGiftPromotionService;
use App\Http\Controllers\Api\CouponController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function __construct(
        protected BuyXGetXPromotionService $promotionService,
        protected FreeGiftPromotionService $freeGiftService
    ) {}

    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart' => 'nullable|array',
            'cart.*.product_id' => 'nullable',
            'cart.*.id' => 'nullable',
            'cart.*.quantity' => 'nullable|integer|min:1',
            'cart.*.price' => 'nullable|numeric|min:0',
            'coupon_code' => 'nullable|string',
            'selected_gift_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $rawCart = $request->input('cart', []);
        $selectedGiftId = $request->input('selected_gift_id') ? (int) $request->input('selected_gift_id') : null;

        // Recalculate Buy X Get X promotion server-side
        $promoResult = $this->promotionService->calculatePromotion($rawCart);

        // Recalculate Free Gift Promotion server-side
        $freeGiftResult = $this->freeGiftService->calculateFreeGift($promoResult['cart_items'], $selectedGiftId);

        $cartItems = $freeGiftResult['cart_items'];

        // Subtotal of paid items only
        $subtotal = 0;
        foreach ($cartItems as $item) {
            if (empty($item['is_free']) && empty($item['is_free_gift'])) {
                $price = (float)($item['price'] ?? 0);
                $qty = (int)($item['quantity'] ?? 1);
                $subtotal += ($price * $qty);
            }
        }

        // Validate coupon on paid items subtotal if provided
        $discount = 0;
        $couponCode = $request->input('coupon_code');
        if ($couponCode) {
            $customer = auth('customer')->user();
            $couponCheck = CouponController::checkValidity($couponCode, $subtotal, $customer);
            if ($couponCheck['valid']) {
                $discount = (float) $couponCheck['discount'];
            }
        }

        $total = max(0, $subtotal - $discount);

        return response()->json([
            'subtotal'   => round($subtotal, 2),
            'discount'   => round($discount, 2),
            'promotion'  => $promoResult['promotion'],
            'free_item'  => $promoResult['free_item'],
            'free_items' => $promoResult['free_items'] ?? [],
            'free_gift'  => $freeGiftResult,
            'cart_items' => $cartItems,
            'total'      => round($total, 2),
        ]);
    }
}
