<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = auth()->user()->cart()->with('book')->get();
        
        // Tính tổng tiền trước khi giảm giá
        $subtotal = $cartItems->sum(function ($item) {
            return $item->book->price * $item->quantity;
        });

        // Xử lý giảm giá
        $discountAmount = 0;
        if (session()->has('applied_discount')) {
            $discountAmount = session('applied_discount.amount');
        }

        return view('cart.index', compact('cartItems', 'subtotal', 'discountAmount'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $book = Book::findOrFail($validated['book_id']);

        // Kiểm tra số lượng tồn kho
        if ($book->quantity < $validated['quantity']) {
            return back()->with('error', 'Số lượng sách trong kho không đủ');
        }

        // Kiểm tra xem sách đã có trong giỏ hàng chưa
        $cartItem = auth()->user()->cart()
            ->where('book_id', $validated['book_id'])
            ->first();

        if ($cartItem) {
            // Nếu có rồi thì cộng thêm số lượng
            $newQuantity = $cartItem->quantity + $validated['quantity'];
            
            if ($book->quantity < $newQuantity) {
                return back()->with('error', 'Số lượng sách trong kho không đủ');
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Nếu chưa có thì tạo mới
            auth()->user()->cart()->create([
                'book_id' => $validated['book_id'],
                'quantity' => $validated['quantity']
            ]);
        }

        return back()->with('success', 'Thêm vào giỏ hàng thành công');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Kiểm tra số lượng tồn kho
        if ($cartItem->book->quantity < $validated['quantity']) {
            return back()->with('error', 'Số lượng sách trong kho không đủ');
        }

        $cartItem->update($validated);

        return back()->with('success', 'Cập nhật số lượng thành công');
    }

    public function remove(CartItem $cartItem)
    {
        if ($cartItem->user_id !== auth()->id()) {
            abort(403);
        }

        $cartItem->delete();

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
    }

    public function clear()
    {
        auth()->user()->cart()->delete();
        
        // Xóa thông tin giảm giá trong session
        session()->forget('applied_discount');

        return back()->with('success', 'Đã xóa toàn bộ giỏ hàng');
    }
} 