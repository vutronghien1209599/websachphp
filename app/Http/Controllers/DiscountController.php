<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function apply(Request $request)
    {
        $code = strtoupper($request->code);
        $discount = Discount::where('code', $code)->first();

        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại'
            ]);
        }

        if (!$discount->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá đã hết hạn hoặc không còn hiệu lực'
            ]);
        }

        // Tính tổng giá trị đơn hàng
        $cartItems = auth()->user()->cart()->with('book')->get();
        $subtotal = $cartItems->sum(function ($item) {
            return $item->book->price * $item->quantity;
        });

        if ($subtotal < $discount->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Giá trị đơn hàng tối thiểu để sử dụng mã này là ' . number_format($discount->min_order_amount) . 'đ'
            ]);
        }

        // Tính số tiền được giảm
        $discountAmount = $discount->calculateDiscount($subtotal);

        // Lưu thông tin giảm giá vào session
        session()->put('applied_discount', [
            'code' => $code,
            'amount' => $discountAmount
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công',
            'discount' => [
                'code' => $code,
                'amount' => $discountAmount,
                'formatted_amount' => number_format($discountAmount) . 'đ',
                'total' => $subtotal + 30000 - $discountAmount,
                'formatted_total' => number_format($subtotal + 30000 - $discountAmount) . 'đ'
            ]
        ]);
    }
} 