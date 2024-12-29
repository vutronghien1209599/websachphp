@extends('layouts.app')

@section('title', 'Danh sách sách')

@section('content')
<div class="container">
    <div class="row mb-4">
        <!-- Phần lọc và tìm kiếm -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Lọc sách</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('books.index') }}" method="GET">
                        <div class="mb-3">
                            <label class="form-label">Danh mục</label>
                            <select name="category" class="form-select">
                                <option value="">Tất cả</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" 
                                            {{ request('category') == $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sắp xếp</label>
                            <select name="sort" class="form-select">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>
                                    Mới nhất
                                </option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                                    Giá tăng dần
                                </option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                                    Giá giảm dần
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Lọc</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Phần danh sách sách -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Danh sách sách</h2>
                <form class="d-flex" action="{{ route('books.index') }}" method="GET">
                    <input type="text" name="search" class="form-control me-2" 
                           placeholder="Tìm kiếm sách..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-outline-primary">Tìm kiếm</button>
                </form>
            </div>

            @if($books->isEmpty())
                <div class="alert alert-info">
                    Không tìm thấy sách nào phù hợp.
                </div>
            @else
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    @foreach($books as $book)
                        <div class="col">
                            <div class="card h-100">
                                <img src="{{ asset('storage/books/'.$book->image) }}" 
                                     class="card-img-top" alt="{{ $book->title }}"
                                     style="height: 300px; object-fit: cover;">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-truncate">{{ $book->title }}</h5>
                                    <p class="card-text text-muted mb-1">{{ $book->author }}</p>
                                    <p class="card-text text-primary fw-bold mb-2">
                                        {{ number_format($book->price) }}đ
                                    </p>
                                    <p class="card-text mb-3">
                                        @if($book->status === 'available')
                                            <span class="badge bg-success">Còn hàng</span>
                                        @else
                                            <span class="badge bg-danger">Hết hàng</span>
                                        @endif
                                    </p>
                                    <div class="mt-auto">
                                        <a href="{{ route('books.show', $book) }}" 
                                           class="btn btn-primary">Chi tiết</a>
                                        @if($book->status === 'available')
                                            <button type="button" class="btn btn-success" 
                                                    onclick="addToCart({{ $book->id }})">
                                                Thêm vào giỏ
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        Showing {{ $books->firstItem() }} to {{ $books->lastItem() }} of {{ $books->total() }} results
                    </div>
                    <div>
                        {{ $books->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function addToCart(bookId) {
    // Kiểm tra đăng nhập
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
                alert('Thêm vào giỏ hàng thành công!');
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
@endsection 