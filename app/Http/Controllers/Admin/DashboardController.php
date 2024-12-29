<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Book;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Tổng doanh thu
        $totalRevenue = Order::where('status', 'completed')
            ->sum('total_amount');

        // Đơn hàng mới
        $newOrders = Order::where('status', 'pending')->count();

        // Tổng số sách
        $totalBooks = Book::count();

        // Tổng số người dùng
        $totalUsers = User::where('role', 'user')->count();

        // Đơn hàng gần đây
        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Sách bán chạy
        $topBooks = DB::table('books')
            ->leftJoin('order_items', 'books.id', '=', 'order_items.book_id')
            ->leftJoin('orders', function($join) {
                $join->on('order_items.order_id', '=', 'orders.id')
                    ->where('orders.status', '=', 'completed');
            })
            ->select(
                'books.*',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold'),
                DB::raw('COALESCE(SUM(order_items.quantity * order_items.price), 0) as revenue')
            )
            ->groupBy('books.id', 'books.title', 'books.author', 'books.price', 'books.image', 'books.description', 'books.created_at', 'books.updated_at')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'newOrders',
            'totalBooks',
            'totalUsers',
            'recentOrders',
            'topBooks'
        ));
    }
} 