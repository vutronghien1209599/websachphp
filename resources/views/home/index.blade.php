@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
<!-- Hero Section với Slider -->
<div class="hero-section mb-5">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('storage/images/banner2.jpg') }}" class="d-block w-100" alt="Banner 1">
                <div class="carousel-caption">
                    <a href="{{ route('books.index') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-book"></i> Khám phá ngay
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Danh mục nổi bật -->
    <section class="featured-categories mb-5">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">Danh mục sách</h2>
            <p class="text-muted">Khám phá các thể loại sách đa dạng</p>
        </div>
        <div class="row g-4">
            @foreach($categories as $category)
            <div class="col-6 col-md-3">
                <a href="{{ route('books.category', $category->slug) }}" class="text-decoration-none">
                    <div class="category-card text-center p-4 rounded shadow-sm">
                        <i class="{{ $category->icon }} text-primary fs-1 mb-3 d-block"></i>
                        <h5 class="mb-2">{{ $category->name }}</h5>
                        <p class="text-muted small mb-0">{{ $category->description }}</p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Sách mới -->
    <section class="new-books mb-5">
        <div class="section-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title mb-1">Sách mới</h2>
                <p class="text-muted mb-0">Những cuốn sách mới nhất tại cửa hàng</p>
            </div>
            <a href="{{ route('books.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-grid"></i> Xem tất cả
            </a>
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            @foreach($newBooks as $book)
            <div class="col">
                <div class="card book-card h-100 border-0 shadow-sm">
                    <div class="position-relative">
                        <div class="book-image">
                            <img src="{{ asset('storage/books/'.$book->image) }}"
                                class="card-img-top"
                                alt="{{ $book->title }}">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="book-category">
                                <span class="badge bg-primary-subtle text-primary">
                                    {{ $book->category->name }}
                                </span>
                            </div>
                            <div class="book-status">
                                @if($book->status === 'available')
                                <span class="badge bg-success-subtle text-success">Còn hàng</span>
                                @else
                                <span class="badge bg-danger-subtle text-danger">Hết hàng</span>
                                @endif
                            </div>
                        </div>
                        <h5 class="card-title">
                            <a href="{{ route('books.show', $book) }}" class="text-decoration-none text-dark">
                                {{ $book->title }}
                            </a>
                        </h5>
                        <p class="card-text text-muted mb-3">{{ $book->author }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small">Giá bán</span>
                                <div class="price text-primary fw-bold">{{ number_format($book->price) }}đ</div>
                            </div>
                            @if($book->status === 'available')
                            <div class="mb-4">
                                <form action="{{ route('cart.add') }}" method="POST" class="d-flex align-items-center">
                                    @csrf
                                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                                    <div class="input-group me-3" style="width: 130px;">
                                        <button class="btn btn-outline-secondary" type="button" onclick="decrementQuantity()">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <input type="number" class="form-control text-center" name="quantity" value="1" min="1" max="{{ $book->quantity }}" id="quantity">
                                        <button class="btn btn-outline-secondary" type="button" onclick="incrementQuantity()">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                    <button type="submit" class="btn btn-primary" {{ $book->quantity == 0 ? 'disabled' : '' }}>
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Sách bán chạy -->
    <section class="best-sellers mb-5">
        <div class="section-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title mb-1">Sách bán chạy</h2>
                <p class="text-muted mb-0">Những cuốn sách được yêu thích nhất</p>
            </div>
            <a href="{{ route('books.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-grid"></i> Xem tất cả
            </a>
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            @foreach($bestSellers as $book)
            <div class="col">
                <div class="card book-card h-100 border-0 shadow-sm">
                    <div class="position-relative">
                        <div class="book-image">
                            <img src="{{ asset('storage/books/'.$book->image) }}"
                                class="card-img-top"
                                alt="{{ $book->title }}">
                        </div>
                        
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="book-category">
                                <span class="badge bg-primary-subtle text-primary">
                                    {{ $book->category->name }}
                                </span>
                            </div>
                            <div class="book-status">
                                @if($book->status === 'available')
                                <span class="badge bg-success-subtle text-success">Còn hàng</span>
                                @else
                                <span class="badge bg-danger-subtle text-danger">Hết hàng</span>
                                @endif
                            </div>
                        </div>
                        <h5 class="card-title">
                            <a href="{{ route('books.show', $book) }}" class="text-decoration-none text-dark">
                                {{ $book->title }}
                            </a>
                        </h5>
                        <p class="card-text text-muted mb-3">{{ $book->author }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small">Giá bán</span>
                                <div class="price text-primary fw-bold">{{ number_format($book->price) }}đ</div>
                            </div>
                            @if($book->status === 'available')
                            <div class="mb-4">
                                <form action="{{ route('cart.add') }}" method="POST" class="d-flex align-items-center">
                                    @csrf
                                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                                    <div class="input-group me-3" style="width: 130px;">
                                        <button class="btn btn-outline-secondary" type="button" onclick="decrementQuantity()">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <input type="number" class="form-control text-center" name="quantity" value="1" min="1" max="{{ $book->quantity }}" id="quantity">
                                        <button class="btn btn-outline-secondary" type="button" onclick="incrementQuantity()">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                    <button type="submit" class="btn btn-primary" {{ $book->quantity == 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-cart-plus"></i>
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

</div>

@push('styles')
<style>
    :root {
        --primary-color: #2c3e50;
        --primary-light: #3498db;
        --primary-dark: #2980b9;
        --success-color: #27ae60;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
        --gray-100: #f8f9fa;
        --gray-200: #e9ecef;
        --gray-300: #dee2e6;
        --gray-400: #ced4da;
        --gray-500: #adb5bd;
        --gray-600: #6c757d;
        --gray-700: #495057;
        --gray-800: #343a40;
        --gray-900: #212529;
    }

    .hero-section {
        margin-top: -1.5rem;
        margin-bottom: 4rem;
    }

    .carousel-item {
        height: 70vh;
        min-height: 500px;
        max-height: 700px;
    }

    .carousel-item img {
        object-fit: cover;
        height: 100%;
        filter: brightness(0.5);
    }

    .carousel-caption {
        background: rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(8px);
        padding: 3rem;
        border-radius: 1.5rem;
        max-width: 700px;
        margin: 0 auto;
        bottom: 50%;
        transform: translateY(50%);
    }

    .carousel-caption h1 {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .carousel-caption p {
        font-size: 1.25rem;
        margin-bottom: 2rem;
    }

    .carousel-caption .btn {
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .section-header {
        text-align: center;
        margin-bottom: 4rem;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 1rem;
        position: relative;
        display: inline-block;
        padding-bottom: 1rem;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: var(--primary-light);
        border-radius: 2px;
    }

    .section-subtitle {
        font-size: 1.1rem;
        color: var(--gray-600);
        max-width: 600px;
        margin: 0 auto;
    }

    .category-card {
        background: white;
        padding: 2rem;
        border-radius: 1rem;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        height: 100%;
    }

    .category-card:hover {
        transform: translateY(-10px);
        border-color: var(--primary-light);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .category-card i {
        font-size: 3rem;
        margin-bottom: 1.5rem;
    }

    .category-card h5 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--gray-800);
    }

    .book-card {
        background: white;
        border-radius: 1rem;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }

    .book-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .book-image {
        height: 350px;
        position: relative;
        overflow: hidden;
    }

    .book-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .book-card:hover .book-image img {
        transform: scale(1.1);
    }

    .book-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: all 0.3s ease;
    }

    .book-card:hover .book-overlay {
        opacity: 1;
    }

    .book-actions {
        display: flex;
        gap: 1rem;
    }

    .book-actions .btn {
        width: 45px;
        height: 45px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        border-radius: 50%;
        background: white;
        color: var(--primary-color);
        transition: all 0.3s ease;
    }

    .book-actions .btn:hover {
        background: var(--primary-light);
        color: white;
        transform: scale(1.1);
    }

    .card-body {
        padding: 1.5rem;
    }

    .badge {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
        font-weight: 500;
        border-radius: 50rem;
    }

    .bg-primary-subtle {
        background: rgba(52, 152, 219, 0.1);
        color: var(--primary-light) !important;
    }

    .bg-success-subtle {
        background: rgba(39, 174, 96, 0.1);
        color: var(--success-color) !important;
    }

    .bg-danger-subtle {
        background: rgba(231, 76, 60, 0.1);
        color: var(--danger-color) !important;
    }

    .price {
        font-size: 1.25rem;
        color: var(--primary-light);
    }

    .promo-banner {
        margin: 5rem 0;
    }

    .promo-card {
        height: 350px;
        border-radius: 1.5rem;
        overflow: hidden;
        position: relative;
        transition: all 0.3s ease;
    }

    .promo-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(45deg, rgba(0, 0, 0, 0.7), transparent);
        z-index: 1;
    }

    .promo-card>div {
        position: relative;
        z-index: 2;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .promo-card:hover {
        transform: translateY(-10px);
    }

    .bg-gradient-primary {
        background: linear-gradient(45deg, var(--primary-color), var(--primary-light));
    }

    .bg-gradient-success {
        background: linear-gradient(45deg, var(--success-color), #2ecc71);
    }

    .newsletter {
        background: var(--gray-100);
        border-radius: 1.5rem;
        overflow: hidden;
        margin: 5rem 0;
    }

    .newsletter img {
        height: 100%;
        object-fit: cover;
    }

    .newsletter-content {
        padding: 4rem;
    }

    .input-group-lg .form-control {
        border-radius: 50rem 0 0 50rem;
        padding: 1rem 1.5rem;
        font-size: 1.1rem;
    }

    .input-group-lg .btn {
        border-radius: 0 50rem 50rem 0;
        padding: 1rem 2rem;
        font-size: 1.1rem;
    }

    @media (max-width: 768px) {
        .carousel-caption {
            padding: 1.5rem;
            max-width: 90%;
        }

        .carousel-caption h1 {
            font-size: 2rem;
        }

        .carousel-item {
            height: 60vh;
        }

        .promo-card {
            height: 250px;
        }

        .newsletter-content {
            padding: 2rem;
        }
    }
</style>
@endpush

@push('scripts')

<script>
function incrementQuantity() {
    const input = document.getElementById('quantity');
    const max = parseInt(input.getAttribute('max'));
    const currentValue = parseInt(input.value);
    if (currentValue < max) {
        input.value = currentValue + 1;
    }
}

function decrementQuantity() {
    const input = document.getElementById('quantity');
    const currentValue = parseInt(input.value);
    if (currentValue > 1) {
        input.value = currentValue - 1;
    }
}
</script>
@endpush

<!-- Toast Notification -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="cartToast" class="toast" role="alert">
        <div class="toast-header">
            <strong class="me-auto">Thông báo</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            Đã thêm sản phẩm vào giỏ hàng!
        </div>
    </div>
</div>

@endsection