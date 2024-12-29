<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = auth()->user()->wishlist()->with('book')->get();
        return view('wishlist.index', compact('wishlist'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id'
        ]);

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => auth()->id(),
            'book_id' => $validated['book_id']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào danh sách yêu thích'
        ]);
    }

    public function remove($id)
    {
        $wishlist = Wishlist::where('user_id', auth()->id())
                          ->where('book_id', $id)
                          ->delete();

        return back()->with('success', 'Đã xóa khỏi danh sách yêu thích');
    }
} 