<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại'
            ]);
        }

        $cartTotal = auth()->user()->cart()->with('book')->get()->sum(function($item) {
            return $item->book->price * $item->quantity;
        });

        if (!$coupon->isValid($cartTotal)) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không hợp lệ'
            ]);
        }

        $discount = $coupon->calculateDiscount($cartTotal);

        session(['coupon' => [
            'code' => $coupon->code,
            'discount' => $discount
        ]]);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công',
            'discount' => $discount
        ]);
    }
} 