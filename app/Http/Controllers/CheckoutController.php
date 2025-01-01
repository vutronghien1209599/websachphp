<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Book;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = auth()->user()->cart()->with('book')->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Giỏ hàng của bạn đang trống');
        }

        // Tính tổng tiền trước khi giảm giá
        $subtotal = $cartItems->sum(function ($item) {
            return $item->book->price * $item->quantity;
        });

        // Xử lý giảm giá
        $discountAmount = 0;
        if (session()->has('applied_discount')) {
            $discountAmount = session('applied_discount.amount');
        }

        return view('cart.checkout', compact('cartItems', 'subtotal', 'discountAmount'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $cartItems = $user->cart()->with('book')->get();

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

            // Kiểm tra số lượng tồn kho
            foreach ($cartItems as $item) {
                $book = Book::lockForUpdate()->find($item->book_id);
                if (!$book || $book->quantity < $item->quantity) {
                    throw new \Exception("Sản phẩm {$book->title} không đủ số lượng trong kho");
                }
            }

            // Tính tổng tiền trước khi giảm giá
            $subtotal = $cartItems->sum(function ($item) {
                return $item->book->price * $item->quantity;
            });

            // Xử lý giảm giá
            $discountAmount = 0;
            $discountId = null;

            if (session()->has('applied_discount')) {
                $discountAmount = session('applied_discount.amount');
                $discount = Discount::where('code', session('applied_discount.code'))->first();
                if ($discount) {
                    $discountId = $discount->id;
                    $discount->increment('used_count');
                }
            }

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $subtotal + 30000 - $discountAmount, // Thêm phí vận chuyển
                'shipping_name' => $validated['shipping_name'],
                'shipping_phone' => $validated['shipping_phone'],
                'shipping_address' => $validated['shipping_address'],
                'note' => $validated['note'],
                'status' => 'pending',
                'discount_id' => $discountId,
                'discount_amount' => $discountAmount,
            ]);

            // Tạo chi tiết đơn hàng và cập nhật số lượng sách
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
            $user->cart()->delete();

            // Xóa thông tin giảm giá trong session
            session()->forget('applied_discount');

            DB::commit();

            // Nếu thanh toán qua VNPAY
            if ($request->payment_method === 'vnpay') {
                return app(VNPayController::class)->createPayment($order);
            }

            return redirect()->route('orders.show', $order)
                ->with('success', 'Đặt hàng thành công');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi đặt hàng: ' . $e->getMessage());
            
            return back()->with('error', $e->getMessage() ?: 'Đã có lỗi xảy ra, vui lòng thử lại');
        }
    }
} 