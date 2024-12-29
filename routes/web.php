<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BookManagementController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\VNPayController;

// Route cho khách
Route::get('/', [HomeController::class, 'index'])->name('home');

// Route xác thực
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// Route cho sách
Route::prefix('books')->group(function () {
    Route::get('/', [BookController::class, 'index'])->name('books.index');
    Route::get('/{book}', [BookController::class, 'show'])->name('books.show');
    Route::get('/category/{category}', [BookController::class, 'category'])->name('books.category');
});

// Route cho user đã đăng nhập
Route::middleware('auth')->group(function () {
    // Quản lý giỏ hàng
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('cart.index');
        Route::post('/add', [CartController::class, 'add'])->name('cart.add');
        Route::put('/{cartItem}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
        Route::delete('/', [CartController::class, 'clear'])->name('cart.clear');
    });

    // Quản lý đơn hàng
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
    });

    // Thanh toán VNPay
    Route::prefix('vnpay')->group(function () {
        Route::get('/create-payment/{order}', [VNPayController::class, 'createPayment'])->name('vnpay.create');
        Route::get('/return', [VNPayController::class, 'return'])->name('vnpay.return');
    });

    // Thông tin cá nhân
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    Route::put('/profile', [HomeController::class, 'updateProfile'])->name('profile.update');
});

// Route cho admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Quản lý sách
    Route::prefix('books')->group(function () {
        Route::get('/', [BookManagementController::class, 'index'])->name('books.index');
        Route::get('/create', [BookManagementController::class, 'create'])->name('books.create');
        Route::post('/', [BookManagementController::class, 'store'])->name('books.store');
        Route::get('/{book}/edit', [BookManagementController::class, 'edit'])->name('books.edit');
        Route::put('/{book}', [BookManagementController::class, 'update'])->name('books.update');
        Route::delete('/{book}', [BookManagementController::class, 'destroy'])->name('books.destroy');
    });

    // Quản lý đơn hàng
    Route::prefix('orders')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::put('/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    });

    // Quản lý người dùng
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
