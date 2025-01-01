<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('orders.show', compact('order'));
    }

    public function checkout(Request $request)
    {
        $user = auth()->user();
        $cartItems = $user->cartItems;

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Giỏ hàng của bạn đang trống');
        }

        $validated = $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'note' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Tính tổng tiền
            $subtotal = $cartItems->sum(function ($item) {
                return $item->book->price * $item->quantity;
            });

            // Xử lý giảm giá
            $discount = Session::get('discount');
            $discountAmount = 0;

            if ($discount) {
                $discountAmount = $discount->calculateDiscount($subtotal);
                // Tăng số lần sử dụng của mã giảm giá
                $discount->incrementUsage();
            }

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $subtotal - $discountAmount,
                'shipping_name' => $validated['shipping_name'],
                'shipping_phone' => $validated['shipping_phone'],
                'shipping_address' => $validated['shipping_address'],
                'note' => $validated['note'],
                'status' => 'pending',
                'discount_id' => $discount ? $discount->id : null,
                'discount_amount' => $discountAmount,
            ]);

            // Tạo chi tiết đơn hàng
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $item->book_id,
                    'quantity' => $item->quantity,
                    'price' => $item->book->price,
                ]);

                // Cập nhật số lượng sách
                $book = $item->book;
                $book->quantity -= $item->quantity;
                $book->save();
            }

            // Xóa giỏ hàng
            $user->cartItems()->delete();

            // Xóa thông tin giảm giá trong session
            Session::forget(['discount', 'discount_code', 'discount_amount']);

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Đặt hàng thành công');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã có lỗi xảy ra, vui lòng thử lại');
        }
    }
}
