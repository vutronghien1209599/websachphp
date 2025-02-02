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
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\PublisherController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\ChatController;

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
    Route::get('/category/{slug}', [BookController::class, 'category'])->name('books.category');
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

    // Áp dụng mã giảm giá
    Route::post('/discounts/apply', [App\Http\Controllers\DiscountController::class, 'apply'])->name('discounts.apply');

    // Thanh toán
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('orders.checkout');

    // Quản lý đơn hàng
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
    });

    // Thanh toán VNPay
    Route::prefix('vnpay')->group(function () {
        Route::get('/create-payment/{order}', [VNPayController::class, 'createPayment'])->name('vnpay.create');
        Route::get('/return', [VNPayController::class, 'return'])->name('vnpay.return');
    });

    // Thông tin cá nhân
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Reviews
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/reviews/{review}/helpful', [ReviewController::class, 'markHelpful'])->name('reviews.helpful');

    // Chat routes
    Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
    Route::post('/chats', [ChatController::class, 'store'])->name('chats.store');
    Route::post('/chats/mark-as-read', [ChatController::class, 'markAsRead'])->name('chats.mark-as-read');
});

// Route cho admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Quản lý danh mục
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

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

    // Quản lý voucher
    Route::resource('discounts', DiscountController::class);

    // Authors
    Route::resource('authors', AuthorController::class);

    // Quản lý nhà xuất bản
    Route::resource('publishers', PublisherController::class);

    // Quản lý reviews
    Route::get('reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::get('reviews/{review}', [App\Http\Controllers\Admin\ReviewController::class, 'show'])->name('reviews.show');
    Route::post('reviews/{review}/approve', [App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('reviews/{review}/reject', [App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('reviews.reject');
    Route::post('reviews/{review}/respond', [App\Http\Controllers\Admin\ReviewController::class, 'respond'])->name('reviews.respond');
    Route::delete('reviews/responses/{response}', [App\Http\Controllers\Admin\ReviewController::class, 'deleteResponse'])->name('reviews.responses.delete');
    Route::delete('reviews/{review}', [App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Frontend routes
Route::get('authors', [\App\Http\Controllers\Admin\AuthorController::class, 'index'])->name('authors.index');
Route::get('authors/{author:slug}', [\App\Http\Controllers\Admin\AuthorController::class, 'show'])->name('authors.show');

Route::get('publishers', [PublisherController::class, 'index'])->name('publishers.index');
Route::get('publishers/{publisher:slug}', [PublisherController::class, 'show'])->name('publishers.show');

// API routes for select2
Route::prefix('api')->name('api.')->middleware(['auth'])->group(function () {
    Route::get('authors/search', [App\Http\Controllers\Admin\AuthorController::class, 'search'])->name('authors.search');
    Route::post('authors', [App\Http\Controllers\Admin\AuthorController::class, 'apiStore'])->name('authors.store');
});
