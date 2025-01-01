@extends('layouts.app')

@section('title', $book->title)

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            @if($book->category)
            <li class="breadcrumb-item">
                <a href="{{ route('books.category', $book->category->slug) }}">
                    {{ $book->category->name }}
                </a>
            </li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $book->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Hình ảnh sách -->
        <div class="col-md-4">
            <div class="position-sticky" style="top: 2rem;">
                <div class="card border-0 shadow-sm">
                    <img src="{{ asset('storage/books/' . $book->image) }}" 
                         class="card-img-top" 
                         alt="{{ $book->title }}"
                         style="object-fit: contain; height: 400px;">
                </div>
            </div>
        </div>

        <!-- Thông tin sách -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h1 class="card-title h2 mb-3">{{ $book->title }}</h1>
                    <p class="text-muted mb-3">
                        <i class="bi bi-person-circle me-2"></i>Tác giả: {{ $book->author }}
                    </p>
                    <p class="text-muted mb-3">
                        <i class="bi bi-bookmark-fill me-2"></i>Thể loại: {{ $book->category ? $book->category->name : 'Chưa phân loại' }}
                    </p>
                    <div class="d-flex align-items-center mb-4">
                        <h3 class="text-primary mb-0 me-3">{{ number_format($book->price) }}đ</h3>
                        @if($book->quantity > 0)
                            <span class="badge bg-success">Còn hàng</span>
                        @else
                            <span class="badge bg-danger">Hết hàng</span>
                        @endif
                    </div>

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
                                <i class="bi bi-cart-plus me-2"></i>Thêm vào giỏ
                            </button>
                        </form>
                    </div>

                    <div class="mb-4">
                        <h5>Mô tả sách</h5>
                        <div class="book-description">
                            {{ $book->description }}
                        </div>
                    </div>

                    <!-- Nút chia sẻ -->
                    <div class="d-flex align-items-center">
                        <span class="me-3">Chia sẻ:</span>
                        <a href="#" class="btn btn-outline-primary btn-sm me-2">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="btn btn-outline-info btn-sm me-2">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-pinterest"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sách liên quan -->
    @if($relatedBooks->count() > 0)
    <div class="mt-5">
        <h3 class="mb-4">Sách liên quan</h3>
        <div class="row">
            @foreach($relatedBooks as $relatedBook)
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="{{ asset('storage/books/' . $relatedBook->image) }}" 
                         class="card-img-top" 
                         alt="{{ $relatedBook->title }}"
                         style="object-fit: contain; height: 200px;">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ route('books.show', $relatedBook) }}" class="text-decoration-none text-dark">
                                {{ Str::limit($relatedBook->title, 40) }}
                            </a>
                        </h5>
                        <p class="card-text text-muted">{{ Str::limit($relatedBook->author, 20) }}</p>
                        <p class="card-text text-primary fw-bold">{{ number_format($relatedBook->price) }}đ</p>
                        @if($relatedBook->quantity > 0)
                            <span class="badge bg-success">Còn hàng</span>
                        @else
                            <span class="badge bg-danger">Hết hàng</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

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

<style>
.book-description {
    line-height: 1.8;
    color: #555;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-5px);
}

.breadcrumb {
    background-color: transparent;
    padding: 0;
    margin-bottom: 2rem;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
}

.breadcrumb-item a {
    color: #6c757d;
    text-decoration: none;
}

.breadcrumb-item a:hover {
    color: #0d6efd;
}

.badge {
    padding: 0.5em 1em;
    font-weight: 500;
}
</style>
@endpush
@endsection
