<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Book;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = auth()->user()->cart()->with('book')->get();
        $total = $cartItems->sum(function($item) {
            return $item->book->price * $item->quantity;
        });
        $shippingFee = 30000; // Phí ship cố định

        return view('cart.index', compact('cartItems', 'total', 'shippingFee'));
    }

    public function add(Request $request, Book $book)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $book->stock
        ]);

        $cart = Cart::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'book_id' => $book->id
            ],
            [
                'quantity' => $request->quantity
            ]
        );

        return redirect()->back()->with('success', 'Thêm vào giỏ hàng thành công');
    }

    public function update(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $cart->book->stock
        ]);

        $cart->update(['quantity' => $request->quantity]);

        return redirect()->back()->with('success', 'Cập nhật số lượng thành công');
    }

    public function remove(Cart $cart)
    {
        $cart->delete();

        return redirect()->back()->with('success', 'Xóa sản phẩm thành công');
    }
} 