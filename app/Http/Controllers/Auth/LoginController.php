<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        // Trả ra giao diện auth.login
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validate dữ liệu được gửi từ form
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            // Trả về trang chủ
            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {   
        // Đăng xuất người dùng
        Auth::logout();
        // Hủy session
        $request->session()->invalidate();
        // Tạo lại token
        $request->session()->regenerateToken();
        // Trả về trang chủ
        return redirect()->route('home');
    }
} 