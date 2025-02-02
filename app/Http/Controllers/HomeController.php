<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy danh sách các thể loại   
        $categories = Category::all();
        
        // Lấy sách mới với phiên bản mới nhất
        $newBooks = Book::where('status', 'active')
            ->with(['category', 'authors', 'editions' => function($q) {
                $q->latest('publication_date');
            }])
            ->latest()
            ->take(8)
            ->get();

        // Lấy sách bán chạy với phiên bản mới nhất
        $bestSellers = Book::where('status', 'active')
            ->withCount('orderItems')
            ->with(['category', 'authors', 'editions' => function($q) {
                $q->latest('publication_date');
            }])
            ->orderByDesc('order_items_count')
            ->take(8)
            ->get();

        // Trả ra giao diện home.index với các biến truyền vào
        return view('home.index', compact('categories', 'newBooks', 'bestSellers'));
    }

    public function profile()
    {
        return view('profile.index');
    }

    public function updateProfile(Request $request)
    {
        // Lấy thông tin người dùng đang đăng nhập
        $user = auth()->user();
        
        // Validate dữ liệu được gửi từ form
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return back()->with('success', 'Thông tin cá nhân đã được cập nhật.');
    }
} 