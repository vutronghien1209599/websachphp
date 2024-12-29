<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');
        $newOrders = Order::where('status', 'pending')->count();
        $totalBooks = Book::count();
        $totalUsers = User::where('role', 'user')->count();

        $recentOrders = Order::with('user')
                            ->latest()
                            ->take(5)
                            ->get();

        $topBooks = Book::withCount(['orderItems as total_sold' => function($query) {
                        $query->whereHas('order', function($q) {
                            $q->where('status', 'completed');
                        });
                    }])
                    ->withSum(['orderItems as revenue' => function($query) {
                        $query->whereHas('order', function($q) {
                            $q->where('status', 'completed');
                        });
                    }], 'price')
                    ->orderBy('total_sold', 'desc')
                    ->take(5)
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