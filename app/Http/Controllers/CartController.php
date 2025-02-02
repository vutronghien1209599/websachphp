<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookEdition;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = auth()->user()->cart()->with(['book.authors', 'bookEdition'])->get();
        
        // Tính tổng tiền trước khi giảm giá
        $subtotal = $cartItems->sum(function ($item) {
            return $item->bookEdition->price * $item->quantity;
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
        // Validate dữ liệu được gửi từ form
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'edition_id' => 'required|exists:book_editions,id',
            'quantity' => 'required|integer|min:1'
        ]);

        // Lấy sách và phiên bản theo id
        $book = Book::findOrFail($validated['book_id']);
        $edition = BookEdition::where('book_id', $book->id)
            ->where('id', $validated['edition_id'])
            ->first();

        if (!$edition) {
            return back()->with('error', 'Không tìm thấy phiên bản sách này');
        }

        // Log thông tin để debug
        \Log::info('Book Edition Info:', [
            'edition_id' => $edition->id,
            'quantity' => $edition->quantity,
            'status' => $edition->status,
            'requested_quantity' => $validated['quantity']
        ]);

        // Kiểm tra số lượng tồn kho của phiên bản
        if ($edition->quantity < $validated['quantity']) {
            return back()->with('error', 'Số lượng sách trong kho không đủ (chỉ còn ' . $edition->quantity . ' cuốn)');
        }

        // Kiểm tra xem sách đã có trong giỏ hàng chưa
        $cartItem = auth()->user()->cart()
            ->where('book_id', $validated['book_id'])
            ->where('edition_id', $validated['edition_id'])
            ->first();

        if ($cartItem) {
            // Nếu có rồi thì cộng thêm số lượng
            $newQuantity = $cartItem->quantity + $validated['quantity'];
            
            // Log thông tin để debug
            \Log::info('Cart Update Info:', [
                'current_quantity' => $cartItem->quantity,
                'add_quantity' => $validated['quantity'],
                'new_quantity' => $newQuantity,
                'stock_quantity' => $edition->quantity
            ]);

            if ($edition->quantity < $newQuantity) {
                return back()->with('error', 'Số lượng sách trong kho không đủ (chỉ còn ' . $edition->quantity . ' cuốn)');
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Nếu chưa có thì tạo mới
            auth()->user()->cart()->create([
                'book_id' => $validated['book_id'],
                'edition_id' => $validated['edition_id'], 
                'quantity' => $validated['quantity']
            ]);
        }

        return back()->with('success', 'Thêm vào giỏ hàng thành công');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        // Kiểm tra xem người dùng có phải là người dùng đang đăng nhập không
        if ($cartItem->user_id !== auth()->id()) {
            abort(403);
        }

        // Validate dữ liệu được gửi từ form
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Kiểm tra số lượng tồn kho của phiên bản
        $edition = $cartItem->bookEdition;
        if (!$edition || $edition->status !== 'available') {
            return back()->with('error', 'Phiên bản sách này hiện không khả dụng');
        }

        if ($edition->quantity < $validated['quantity']) {
            return back()->with('error', 'Số lượng sách trong kho không đủ (chỉ còn ' . $edition->quantity . ' cuốn)');
        }

        $cartItem->update($validated);

        return back()->with('success', 'Cập nhật số lượng thành công');
    }

    public function remove(CartItem $cartItem)
    {
        // Kiểm tra xem người dùng có phải là người dùng đang đăng nhập không
        if ($cartItem->user_id !== auth()->id()) {
            abort(403);
        }

        $cartItem->delete();

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
    }

    public function clear()
    {
        // Xóa toàn bộ giỏ hàng
        auth()->user()->cart()->delete();
        
        // Xóa thông tin giảm giá trong session
        session()->forget('applied_discount');

        return back()->with('success', 'Đã xóa toàn bộ giỏ hàng');
    }
} 