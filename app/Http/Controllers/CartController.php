<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Book;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = auth()->user()->cartItems()->with('book')->get();
        $total = $cartItems->sum(function($item) {
            return $item->quantity * $item->book->price;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $book = Book::findOrFail($request->book_id);

        // Kiểm tra số lượng tồn kho
        if ($book->stock < $request->quantity) {
            return back()->with('error', 'Số lượng sách trong kho không đủ.');
        }

        // Kiểm tra nếu sách đã có trong giỏ hàng
        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('book_id', $request->book_id)
            ->first();

        if ($cartItem) {
            // Cập nhật số lượng nếu đã có trong giỏ
            $newQuantity = $cartItem->quantity + $request->quantity;
            if ($book->stock < $newQuantity) {
                return back()->with('error', 'Số lượng sách trong kho không đủ.');
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Thêm mới vào giỏ hàng
            CartItem::create([
                'user_id' => auth()->id(),
                'book_id' => $request->book_id,
                'quantity' => $request->quantity
            ]);
        }

        return back()->with('success', 'Đã thêm sách vào giỏ hàng.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        if ($cartItem->book->stock < $request->quantity) {
            return back()->with('error', 'Số lượng sách trong kho không đủ.');
        }

        $cartItem->update(['quantity' => $request->quantity]);
        return back()->with('success', 'Đã cập nhật số lượng.');
    }

    public function remove(CartItem $cartItem)
    {
        $cartItem->delete();
        return back()->with('success', 'Đã xóa sách khỏi giỏ hàng.');
    }

    public function clear()
    {
        auth()->user()->cartItems()->delete();
        return back()->with('success', 'Đã xóa toàn bộ giỏ hàng.');
    }
} 