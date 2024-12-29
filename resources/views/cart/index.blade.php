@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h1 class="h3 mb-4">Giỏ hàng của bạn</h1>

                    @if($cartItems->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-cart-x display-1 text-muted"></i>
                            <p class="mt-3">Giỏ hàng trống</p>
                            <a href="{{ route('books.index') }}" class="btn btn-primary">
                                <i class="bi bi-arrow-left me-2"></i>Tiếp tục mua sắm
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-end">Giá</th>
                                        <th class="text-end">Tổng</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cartItems as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ asset('storage/books/' . $item->book->image) }}" 
                                                         alt="{{ $item->book->title }}"
                                                         class="me-3"
                                                         style="width: 60px; height: 80px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <a href="{{ route('books.show', $item->book) }}" 
                                                               class="text-decoration-none text-dark">
                                                                {{ $item->book->title }}
                                                            </a>
                                                        </h6>
                                                        <small class="text-muted">{{ $item->book->author }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center" style="width: 200px;">
                                                <form action="{{ route('cart.update', $item) }}" 
                                                      method="POST" 
                                                      class="d-flex align-items-center justify-content-center">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="input-group" style="width: 120px;">
                                                        <button class="btn btn-outline-secondary" type="button" 
                                                                onclick="this.parentNode.querySelector('input').stepDown()">
                                                            <i class="bi bi-dash"></i>
                                                        </button>
                                                        <input type="number" class="form-control text-center" 
                                                               name="quantity" value="{{ $item->quantity }}" 
                                                               min="1" max="{{ $item->book->stock }}"
                                                               onchange="this.form.submit()">
                                                        <button class="btn btn-outline-secondary" type="button"
                                                                onclick="this.parentNode.querySelector('input').stepUp()">
                                                            <i class="bi bi-plus"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td class="text-end">{{ number_format($item->book->price) }}đ</td>
                                            <td class="text-end">{{ number_format($item->book->price * $item->quantity) }}đ</td>
                                            <td class="text-end">
                                                <form action="{{ route('cart.remove', $item) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger p-0">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('books.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left me-2"></i>Tiếp tục mua sắm
                            </a>
                            <form action="{{ route('cart.clear') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="bi bi-trash me-2"></i>Xóa giỏ hàng
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Tổng đơn hàng</h5>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span>Tạm tính:</span>
                        <span>{{ number_format($total) }}đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Phí vận chuyển:</span>
                        <span>30,000đ</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <strong>Tổng cộng:</strong>
                        <strong class="text-primary">{{ number_format($total + 30000) }}đ</strong>
                    </div>

                    @if(!$cartItems->isEmpty())
                        <form action="{{ route('orders.checkout') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Phương thức thanh toán</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" 
                                           id="cod" value="cod" checked>
                                    <label class="form-check-label" for="cod">
                                        Thanh toán khi nhận hàng
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" 
                                           id="vnpay" value="vnpay">
                                    <label class="form-check-label" for="vnpay">
                                        Thanh toán qua VNPAY
                                        <img src="{{ asset('images/vnpay.png') }}" alt="VNPAY" height="20">
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-credit-card me-2"></i>Tiến hành thanh toán
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
