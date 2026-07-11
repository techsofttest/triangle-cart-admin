<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string',
            'subtotal' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['valid' => false, 'message' => 'Coupon code is required.'], 422);
        }

        $coupon = Coupon::where('coupon_code', $request->input('coupon_code'))->first();

        if (!$coupon) {
            return response()->json(['valid' => false, 'message' => 'Invalid coupon code'], 422);
        }

        $today = Carbon::today()->toDateString();
        if (($coupon->coupon_fromdate && $coupon->coupon_fromdate > $today) || ($coupon->coupon_todate && $coupon->coupon_todate < $today)) {
            return response()->json(['valid' => false, 'message' => 'Coupon is not available today'], 422);
        }

        $subtotal = (float) ($request->input('subtotal') ?? 0);
        $discount = 0;

        if ((int) $coupon->coupon_type === 1) {
            $discount = round($subtotal * ((float) $coupon->coupon_amount / 100), 2);
        } else {
            $discount = min((float) $coupon->coupon_amount, $subtotal);
        }

        return response()->json([
            'valid' => true,
            'coupon' => [
                'coupon_code' => $coupon->coupon_code,
                'coupon_type' => $coupon->coupon_type,
                'coupon_amount' => (float) $coupon->coupon_amount,
            ],
            'discount' => $discount,
            'message' => 'Coupon applied successfully',
        ]);
    }
}
