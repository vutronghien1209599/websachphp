<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Kiểm tra xem đơn hàng có phải của user hiện tại không
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['orderItems.book']);
        return view('orders.show', compact('order'));
    }

    public function checkout(Request $request)
    {
        $cartItems = auth()->user()->cartItems()->with('book')->get();
        
        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Giỏ hàng trống.');
        }

        // Kiểm tra số lượng tồn kho
        foreach ($cartItems as $item) {
            if ($item->book->stock < $item->quantity) {
                return back()->with('error', "Sách '{$item->book->title}' không đủ số lượng trong kho.");
            }
        }

        // Tính tổng tiền
        $total = $cartItems->sum(function($item) {
            return $item->quantity * $item->book->price;
        });

        // Tạo đơn hàng
        $order = Order::create([
            'user_id' => auth()->id(),
            'total_amount' => $total + 30000, // Cộng thêm phí vận chuyển
            'shipping_address' => auth()->user()->address,
            'payment_method' => $request->payment_method,
            'status' => 'pending'
        ]);

        // Tạo chi tiết đơn hàng và cập nhật số lượng tồn kho
        foreach ($cartItems as $item) {
            $order->orderItems()->create([
                'book_id' => $item->book_id,
                'quantity' => $item->quantity,
                'price' => $item->book->price
            ]);

            // Cập nhật số lượng tồn kho
            $item->book->decrement('stock', $item->quantity);
        }

        // Xóa giỏ hàng
        auth()->user()->cartItems()->delete();

        // Lưu lịch sử đơn hàng
        $order->history()->create([
            'status' => 'pending',
            'note' => 'Đơn hàng mới được tạo'
        ]);

        // Nếu thanh toán qua VNPAY
        if ($request->payment_method === 'vnpay') {
            return redirect()->route('vnpay.create', $order);
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Đặt hàng thành công.');
    }
}
