<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FreeGiftPromotionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FreeGiftController extends Controller
{
    public function __construct(
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
            'selected_gift_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cart = $request->input('cart', []);
        $selectedGiftId = $request->input('selected_gift_id') ? (int) $request->input('selected_gift_id') : null;

        $result = $this->freeGiftService->calculateFreeGift($cart, $selectedGiftId);

        return response()->json($result);
    }

    public function select(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:products,id',
            'cart' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $productId = (int) $request->input('product_id');
        $cart = $request->input('cart', []);

        $result = $this->freeGiftService->calculateFreeGift($cart, $productId);

        if (! $result['eligible']) {
            return response()->json([
                'error' => $result['excluded_category_message'] ?? 'Cart is not eligible for a free gift.',
                'free_gift' => $result,
            ], 422);
        }

        if (empty($result['selected_gift']) || (int) $result['selected_gift']['id'] !== $productId) {
            return response()->json([
                'error' => 'Selected product is not a valid free gift option or is out of stock.',
                'free_gift' => $result,
            ], 422);
        }

        return response()->json([
            'message' => 'Free gift selected successfully.',
            'free_gift' => $result,
            'cart_items' => $result['cart_items'],
        ]);
    }
}
