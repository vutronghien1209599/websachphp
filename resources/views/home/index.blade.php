@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
<!-- Hero Section với Slider -->
<div class="hero-section mb-5">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('images/banner1.jpg') }}" class="d-block w-100" alt="Banner 1">
                <div class="carousel-caption">
                    <h1>Chào mừng đến với Book Store</h1>
                    <p>Khám phá kho tàng sách phong phú của chúng tôi</p>
                    <a href="{{ route('books.index') }}" class="btn btn-primary btn-lg">Khám phá ngay</a>
                </div>
            </div>
            <!-- Thêm các slide khác tương tự -->
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</div>

<div class="container">
    <!-- Danh mục nổi bật -->
    <section class="featured-categories mb-5">
        <h2 class="section-title text-center mb-4">Danh mục sách</h2>
        <div class="row g-4">
            @foreach(['Văn học', 'Kinh tế', 'Kỹ năng sống', 'Thiếu nhi'] as $category)
            <div class="col-6 col-md-3">
                <a href="{{ route('books.index', ['category' => $category]) }}" class="text-decoration-none">
                    <div class="category-card text-center p-4 rounded shadow-sm">
                        <i class="bi bi-book fs-1 mb-3 d-block"></i>
                        <h5 class="mb-0">{{ $category }}</h5>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Sách mới -->
    <section class="new-books mb-5">
        <div class="section-header d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title">Sách mới</h2>
            <a href="{{ route('books.index') }}" class="btn btn-outline-primary">Xem tất cả</a>
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            @foreach($newBooks as $book)
            <div class="col">
                <div class="card book-card h-100 border-0 shadow-sm">
                    <div class="position-relative">
                        <div class="book-image" style="height: 300px; overflow: hidden;">
                            <img src="{{ asset('storage/books/'.$book->image) }}" 
                                 class="card-img-top" 
                                 alt="{{ $book->title }}"
                                 style="height: 100%; object-fit: cover;">
                        </div>
                        @if($book->status === 'available')
                        <button class="btn btn-primary quick-add-btn" 
                                onclick="addToCart({{ $book->id }})">
                            <i class="bi bi-cart-plus"></i>
                        </button>
                        @endif
                    </div>
                    <div class="card-body d-flex flex-column">
                        <p class="book-category text-muted small mb-2">{{ $book->category }}</p>
                        <h5 class="card-title text-truncate" title="{{ $book->title }}">
                            {{ $book->title }}
                        </h5>
                        <p class="card-text text-muted mb-1">{{ $book->author }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="price text-primary fw-bold">{{ number_format($book->price) }}đ</span>
                            @if($book->status === 'available')
                                <span class="badge bg-success">Còn hàng</span>
                            @else
                                <span class="badge bg-danger">Hết hàng</span>
                            @endif
                        </div>
                        <div class="mt-auto pt-3">
                            <a href="{{ route('books.show', $book) }}" 
                               class="btn btn-outline-primary w-100">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Banner quảng cáo -->
    <section class="promo-banner mb-5">
        <div class="row">
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="promo-card bg-primary text-white p-4 rounded">
                    <h3>Giảm giá 20%</h3>
                    <p>Cho tất cả sách văn học</p>
                    <a href="#" class="btn btn-light">Xem ngay</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="promo-card bg-success text-white p-4 rounded">
                    <h3>Sách mới về</h3>
                    <p>Đón đọc những tựa sách hot nhất</p>
                    <a href="#" class="btn btn-light">Khám phá</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Sách bán chạy -->
    <section class="best-sellers mb-5">
        <h2 class="section-title text-center mb-4">Sách bán chạy</h2>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <!-- Tương tự như phần sách mới -->
        </div>
    </section>

    <!-- Đăng ký nhận tin -->
    <section class="newsletter bg-light p-5 rounded mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h3>Đăng ký nhận thông tin</h3>
                <p class="text-muted">Nhận thông tin về sách mới và khuyến mãi hấp dẫn</p>
                <form class="newsletter-form">
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Email của bạn">
                        <button class="btn btn-primary" type="submit">Đăng ký</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

@push('styles')
<style>
.hero-section {
    margin-top: -1.5rem;
}

.carousel-item {
    height: 500px;
}

.carousel-item img {
    object-fit: cover;
    height: 100%;
}

.carousel-caption {
    background: rgba(0, 0, 0, 0.5);
    padding: 2rem;
    border-radius: 10px;
}

.section-title {
    position: relative;
    padding-bottom: 15px;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 3px;
    background: #0d6efd;
}

.category-card {
    background: white;
    transition: transform 0.3s;
}

.category-card:hover {
    transform: translateY(-5px);
}

.book-card {
    transition: transform 0.3s;
}

.book-card:hover {
    transform: translateY(-5px);
}

.quick-add-btn {
    position: absolute;
    right: 10px;
    top: 10px;
    opacity: 0;
    transition: opacity 0.3s;
}

.book-card:hover .quick-add-btn {
    opacity: 1;
}

.promo-card {
    height: 200px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background-size: cover;
    background-position: center;
    transition: transform 0.3s;
}

.promo-card:hover {
    transform: scale(1.02);
}

.newsletter {
    background-image: linear-gradient(45deg, #f8f9fa 0%, #e9ecef 100%);
}
</style>
@endpush

@push('scripts')
<script>
function addToCart(bookId) {
    @auth
        fetch(`/cart/add/${bookId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Thêm thông báo toast thay vì alert
                const toast = new bootstrap.Toast(document.getElementById('cartToast'));
                toast.show();
            } else {
                alert(data.message || 'Có lỗi xảy ra');
            }
        })
        .catch(error => {
            alert('Có lỗi xảy ra');
        });
    @else
        window.location.href = '{{ route('login') }}';
    @endauth
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