@extends('layouts.app')

@section('title', 'Danh sách sách')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Phần lọc và tìm kiếm -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-funnel"></i> Lọc sách
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('books.index') }}" method="GET">
                        <div class="mb-3">
                            <label class="form-label">Danh mục</label>
                            <select name="category" class="form-select">
                                <option value="">Tất cả danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->slug }}" 
                                            {{ request('category') == $category->slug ? 'selected' : '' }}>
                                        <i class="{{ $category->icon }}"></i> {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sắp xếp</label>
                            <select name="sort" class="form-select">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>
                                    Mới nhất trước
                                </option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                                    Giá tăng dần
                                </option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                                    Giá giảm dần
                                </option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>
                                    Tên A-Z
                                </option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>
                                    Tên Z-A
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Khoảng giá</label>
                            <div class="input-group mb-2">
                                <input type="number" name="price_from" class="form-control" 
                                       placeholder="Từ" value="{{ request('price_from') }}">
                                <span class="input-group-text">đ</span>
                            </div>
                            <div class="input-group">
                                <input type="number" name="price_to" class="form-control" 
                                       placeholder="Đến" value="{{ request('price_to') }}">
                                <span class="input-group-text">đ</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel"></i> Lọc kết quả
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Phần danh sách sách -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Danh sách sách</h2>
                    <p class="text-muted mb-0">Hiển thị {{ $books->count() }} / {{ $books->total() }} cuốn sách</p>
                </div>
                <div class="d-flex gap-2">
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary active">
                            <i class="bi bi-grid-3x3"></i>
                        </button>
                        <button type="button" class="btn btn-outline-primary">
                            <i class="bi bi-list"></i>
                        </button>
                    </div>
                </div>
            </div>

            @if($books->isEmpty())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Không tìm thấy sách nào phù hợp với tiêu chí tìm kiếm.
                </div>
            @else
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    @foreach($books as $book)
                    <div class="col">
                        <div class="card book-card h-100 border-0 shadow-sm">
                            <div class="position-relative book-cover">
                                <a href="{{ route('books.show', $book) }}">
                                    <img src="{{ asset('storage/books/'.$book->image) }}" 
                                         class="card-img-top" 
                                         alt="{{ $book->title }}">
                                </a>
                                <div class="book-actions">
                                    @if($book->total_quantity > 0)
                                    <button class="btn btn-primary btn-sm" data-add-to-cart="{{ $book->id }}" data-book-title="{{ $book->title }}">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="book-category">
                                        <span class="badge bg-primary-subtle text-primary">
                                            {{ $book->category ? $book->category->name : 'Chưa phân loại' }}
                                        </span>
                                    </div>
                                    <div class="book-status">
                                        @if($book->total_quantity > 0)
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
                                <p class="card-text text-muted mb-3">
                                    @foreach($book->authors as $author)
                                        {{ $author->name }}@if(!$loop->last), @endif
                                    @endforeach
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="price">
                                        <span class="text-muted small">Giá bán</span>
                                        <div class="text-primary fw-bold">{{ number_format($book->default_price) }}đ</div>
                                    </div>
                                    @if($book->total_quantity > 0)
                                    <div class="mb-4">
                                        <form action="{{ route('cart.add') }}" method="POST" class="d-flex align-items-center">
                                            @csrf
                                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                                            <div class="input-group me-3" style="width: 130px;">
                                                <button class="btn btn-outline-secondary" type="button" onclick="decrementQuantity()">
                                                    <i class="bi bi-dash"></i>
                                                </button>
                                                <input type="number" class="form-control text-center" name="quantity" value="1" min="1" max="{{ $book->total_quantity }}" id="quantity">
                                                <button class="btn btn-outline-secondary" type="button" onclick="incrementQuantity()">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
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

                <div class="d-flex justify-content-center mt-4">
                    {{ $books->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.book-card {
    transition: all 0.3s ease;
}

.book-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}

.book-cover {
    overflow: hidden;
    position: relative;
}

.book-cover img {
    height: 300px;
    object-fit: cover;
    transition: all 0.3s ease;
}

.book-cover:hover img {
    transform: scale(1.05);
}

.book-actions {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    gap: 5px;
    opacity: 0;
    transition: all 0.3s ease;
}

.book-cover:hover .book-actions {
    opacity: 1;
}

.book-actions .btn {
    width: 35px;
    height: 35px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255,255,255,0.9);
    border: none;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.book-actions .btn-primary {
    color: var(--primary-color);
}

.book-actions .btn-danger {
    color: var(--danger-color);
}

.book-actions .btn:hover {
    transform: scale(1.1);
}

.card-title {
    font-size: 1rem;
    font-weight: 600;
    line-height: 1.4;
    height: 2.8em;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.price {
    line-height: 1.2;
}

.price .text-primary {
    font-size: 1.1rem;
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