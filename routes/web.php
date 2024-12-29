<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BookManagementController;
use App\Http\Controllers\Admin\OrderManagementController;
use App\Http\Controllers\Admin\UserManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route cho trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Route xác thực
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route cho user đã đăng nhập
Route::middleware('auth')->group(function () {
    // Quản lý giỏ hàng
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('cart.index');
        Route::post('/add/{book}', [CartController::class, 'add'])->name('cart.add');
        Route::patch('/update/{cart}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/remove/{cart}', [CartController::class, 'remove'])->name('cart.remove');
    });

    // Quản lý đơn hàng
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
    });

    // Thông tin cá nhân
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    Route::put('/profile', [HomeController::class, 'updateProfile'])->name('profile.update');
});

// Route cho sách
Route::prefix('books')->group(function () {
    Route::get('/', [BookController::class, 'index'])->name('books.index');
    Route::get('/{book}', [BookController::class, 'show'])->name('books.show');
    Route::get('/category/{category}', [BookController::class, 'category'])->name('books.category');
});

// Route cho admin
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

    // Quản lý sách
    Route::prefix('books')->group(function () {
        Route::get('/', [BookManagementController::class, 'index'])->name('admin.books.index');
        Route::get('/create', [BookManagementController::class, 'create'])->name('admin.books.create');
        Route::post('/', [BookManagementController::class, 'store'])->name('admin.books.store');
        Route::get('/{book}/edit', [BookManagementController::class, 'edit'])->name('admin.books.edit');
        Route::put('/{book}', [BookManagementController::class, 'update'])->name('admin.books.update');
        Route::delete('/{book}', [BookManagementController::class, 'destroy'])->name('admin.books.destroy');
    });

    // Quản lý đơn hàng
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderManagementController::class, 'index'])->name('admin.orders.index');
        Route::get('/{order}', [OrderManagementController::class, 'show'])->name('admin.orders.show');
        Route::patch('/{order}/status', [OrderManagementController::class, 'updateStatus'])->name('admin.orders.status');
    });

    // Quản lý người dùng
    Route::prefix('users')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('admin.users.index');
        Route::get('/{user}', [UserManagementController::class, 'show'])->name('admin.users.show');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
    });
});
