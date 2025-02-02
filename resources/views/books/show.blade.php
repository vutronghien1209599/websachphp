@extends('layouts.app')

@section('title', $book->title)

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<style>
#toast-container > div {
    opacity: 1;
    -ms-filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=100);
    filter: alpha(opacity=100);
}
.toast {
    background-color: #030303;
}
.toast-success {
    background-color: #51a351;
}
.toast-error {
    background-color: #bd362f;
}
.toast-info {
    background-color: #2f96b4;
}
.toast-warning {
    background-color: #f89406;
}
.rating {
    display: flex;
    flex-direction: row-reverse;
    gap: 5px;
}

.rating input[type="radio"] {
    display: none;
}

.rating label {
    cursor: pointer;
    font-size: 25px;
    color: #ddd;
    transition: all 0.2s ease;
}

.rating label:hover,
.rating label:hover ~ label,
.rating input[type="radio"]:checked ~ label {
    color: #ffd700;
}

.rating label:hover:before,
.rating label:hover ~ label:before,
.rating input[type="radio"]:checked ~ label:before {
    content: "★";
}

.rating label:before {
    content: "★";
}
</style>
@endpush

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
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <span class="text-warning">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $book->average_rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </span>
                            <span class="ms-2">{{ number_format($book->average_rating, 1) }}/5</span>
                        </div>
                        <span class="text-muted">({{ $book->reviews_count }} đánh giá)</span>
                    </div>
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

    <!-- Phần đánh giá -->
    <div class="row mt-5">
        <div class="col-12">
            <h2 class="mb-4">Đánh giá từ độc giả</h2>
            
            <!-- Tổng quan đánh giá -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <h1 class="display-4">{{ number_format($book->average_rating, 1) }}</h1>
                            <div class="text-warning mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $book->average_rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <p class="text-muted">{{ $book->reviews_count }} đánh giá</p>
                        </div>
                        <div class="col-md-8">
                            @php
                                $ratings = $book->reviews()->approved()->selectRaw('rating, count(*) as count')
                                    ->groupBy('rating')
                                    ->orderByDesc('rating')
                                    ->get();
                            @endphp
                            @foreach ($ratings as $rating)
                                <div class="d-flex align-items-center mb-2">
                                    <div class="text-warning me-2">{{ $rating->rating }} <i class="fas fa-star"></i></div>
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar bg-warning" 
                                             style="width: {{ ($rating->count / $book->reviews_count) * 100 }}%">
                                        </div>
                                    </div>
                                    <div class="ms-2 text-muted small">{{ $rating->count }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form đánh giá -->
            @auth
                @if (!$book->reviews()->where('user_id', auth()->id())->exists())
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Viết đánh giá của bạn</h5>
                            <form action="{{ route('reviews.store', $book) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Đánh giá:</label>
                                    <div class="rating">
                                        @for ($i = 5; $i >= 1; $i--)
                                            <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ old('rating', 5) == $i ? 'checked' : '' }}>
                                            <label for="star{{ $i }}"></label>
                                        @endfor
                                    </div>
                                    @error('rating')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <input type="hidden" name="book_edition_id" value="0">

                                <div class="mb-3">
                                    <label class="form-label">Nhận xét của bạn:</label>
                                    <textarea name="comment" rows="4" class="form-control @error('comment') is-invalid @enderror" 
                                              required minlength="10" 
                                              placeholder="Chia sẻ trải nghiệm đọc sách của bạn...">{{ old('comment') }}</textarea>
                                    @error('comment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Ưu điểm:</label>
                                        <textarea name="pros" rows="3" class="form-control @error('pros') is-invalid @enderror"
                                                  placeholder="Điểm mạnh của sách...">{{ old('pros') }}</textarea>
                                        @error('pros')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nhược điểm:</label>
                                        <textarea name="cons" rows="3" class="form-control @error('cons') is-invalid @enderror"
                                                  placeholder="Điểm yếu của sách...">{{ old('cons') }}</textarea>
                                        @error('cons')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Gửi đánh giá
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @else
                <div class="alert alert-info">
                    Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để viết đánh giá
                </div>
            @endauth

            <!-- Danh sách đánh giá -->
            <div class="reviews">
                @foreach ($book->reviews()->approved()->with(['user', 'bookEdition', 'responses.admin'])->latest()->get() as $review)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    ok
                                    <h6 class="mb-0">{{ $review->user->name }}</h6>
                                    <div class="text-warning">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $review->rating)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <div class="text-muted small">
                                    {{ $review->formatted_date }}
                                    @if ($review->is_verified_purchase)
                                        <span class="badge bg-success ms-2">
                                            <i class="fas fa-check-circle"></i> Đã mua hàng
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if ($review->bookEdition)
                                <div class="small text-muted mb-2">
                                    Phiên bản: {{ $review->bookEdition->formatted_edition }}
                                </div>
                            @endif

                            <p class="mb-2">{{ $review->comment }}</p>

                            @if ($review->pros)
                                <div class="mb-2">
                                    <strong class="text-success"><i class="fas fa-plus-circle"></i> Ưu điểm:</strong>
                                    <p class="mb-0">{{ $review->pros }}</p>
                                </div>
                            @endif

                            @if ($review->cons)
                                <div class="mb-2">
                                    <strong class="text-danger"><i class="fas fa-minus-circle"></i> Nhược điểm:</strong>
                                    <p class="mb-0">{{ $review->cons }}</p>
                                </div>
                            @endif

                            <div class="d-flex align-items-center mt-3">
                                <button class="btn btn-sm btn-outline-secondary me-2 helpful-btn" 
                                        data-review-id="{{ $review->id }}"
                                        @guest disabled @endguest
                                        @auth @if ($review->user_id === auth()->id()) disabled @endif @endauth>
                                    <i class="far fa-thumbs-up"></i>
                                    Hữu ích (<span class="helpful-count">{{ $review->helpful_count }}</span>)
                                </button>

                                @if ($review->user_id === auth()->id() && $review->is_editable)
                                    <button class="btn btn-sm btn-outline-primary me-2" 
                                            onclick="editReview({{ $review->id }})">
                                        <i class="fas fa-edit"></i> Sửa
                                    </button>
                                    <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Bạn có chắc muốn xóa đánh giá này?')">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <!-- Phản hồi từ admin -->
                            @foreach ($review->responses as $response)
                                <div class="border-start ps-3 mt-3">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>Phản hồi từ Admin</strong>
                                            <small class="text-muted ms-2">{{ $response->formatted_date }}</small>
                                        </div>
                                    </div>
                                    <p class="mb-0">{{ $response->content }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(document).ready(function() {
    // Cấu hình toastr
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    // Hiển thị thông báo nếu có
    @if(session('success'))
        toastr.success('{{ session('success') }}', 'Thành công');
    @endif

    @if(session('error'))
        toastr.error('{{ session('error') }}', 'Lỗi');
    @endif

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

    // Xử lý nút Hữu ích
    $('.helpful-btn').click(function() {
        const btn = $(this);
        const reviewId = btn.data('review-id');
        
        $.post(`/reviews/${reviewId}/helpful`, {
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            if (response.success) {
                btn.find('.helpful-count').text(response.helpfulCount);
                toastr.success(response.message);
            } else {
                toastr.error(response.message);
            }
        })
        .fail(function() {
            toastr.error('Có lỗi xảy ra. Vui lòng thử lại sau.');
        });
    });

    function editReview(reviewId) {
        // Hiển thị modal chỉnh sửa review
        // TODO: Implement edit review modal
    }

    // Hiển thị số sao đã chọn
    $('.rating input[type="radio"]').change(function() {
        const rating = $(this).val();
        console.log('Đã chọn ' + rating + ' sao');
    });
});
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

.rating {
    display: flex;
    flex-direction: row-reverse;
    gap: 5px;
}

.rating input[type="radio"] {
    display: none;
}

.rating label {
    cursor: pointer;
    font-size: 25px;
    color: #ddd;
    transition: all 0.2s ease;
}

.rating label:hover,
.rating label:hover ~ label,
.rating input[type="radio"]:checked ~ label {
    color: #ffd700;
}

.rating label:hover:before,
.rating label:hover ~ label:before,
.rating input[type="radio"]:checked ~ label:before {
    content: "★";
}

.rating label:before {
    content: "★";
}
</style>
@endpush
@endsection
