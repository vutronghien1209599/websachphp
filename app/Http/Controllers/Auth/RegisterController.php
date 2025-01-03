<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validate dữ liệu được gửi từ form
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string'
        ]);

        // Mã hóa mật khẩu
        // Ví dụ mk của anh là 123456789
        // Sau khi mã hóa sẽ là $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
        $validated['password'] = Hash::make($validated['password']);
        // Gán role cho người dùng
        $validated['role'] = 'user';

        $user = User::create($validated);

        auth()->login($user);

        return redirect()->route('home')->with('success', 'Đăng ký tài khoản thành công!');
    }
} 