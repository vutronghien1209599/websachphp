<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $newBooks = Book::where('status', 'available')
                       ->latest()
                       ->take(8)
                       ->get();

        return view('home.index', compact('newBooks'));
    }

    public function profile()
    {
        return view('profile.index');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);

        auth()->user()->update($request->only([
            'full_name', 'phone_number', 'address', 'email'
        ]));

        return redirect()->back()->with('success', 'Cập nhật thông tin thành công');
    }
} 