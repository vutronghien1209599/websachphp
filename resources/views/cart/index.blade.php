@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2>Giỏ hàng của bạn</h2>
        @if($cartItems->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Tổng</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('storage/books/'.$item->book->image) }}" 
                                             alt="{{ $item->book->title }}" 
                                             style="width: 50px; margin-right: 10px;">
                                        <div>
                                            <h6 class="mb-0">{{ $item->book->title }}</h6>
                                            <small class="text-muted">{{ $item->book->author }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ number_format($item->book->price) }}đ</td>
                                <td>
                                    <form action="{{ route('cart.update', $item) }}" 
                                          method="POST" class="d-flex align-items-center">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" 
                                               value="{{ $item->quantity }}" 
                                               min="1" max="{{ $item->book->stock }}" 
                                               class="form-control" style="width: 70px">
                                        <button type="submit" class="btn btn-sm btn-outline-primary ms-2">
                                            Cập nhật
                                        </button>
                                    </form>
                                </td>
                                <td>{{ number_format($item->book->price * $item->quantity) }}đ</td>
                                <td>
                                    <form action="{{ route('cart.remove', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                Giỏ hàng của bạn đang trống. 
                <a href="{{ route('books.index') }}" class="alert-link">Mua sắm ngay</a>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        @if($cartItems->count() > 0)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tổng đơn hàng</h5>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Tạm tính:</span>
                        <span>{{ number_format($total) }}đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Phí vận chuyển:</span>
                        <span>{{ number_format($shippingFee) }}đ</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Tổng cộng:</strong>
                        <strong>{{ number_format($total + $shippingFee) }}đ</strong>
                    </div>
                    <form action="{{ route('orders.checkout') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ giao hàng</label>
                            <textarea name="shipping_address" class="form-control" required
                                      rows="3">{{ auth()->user()->address }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phương thức thanh toán</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cod">Thanh toán khi nhận hàng</option>
                                <option value="banking">Chuyển khoản ngân hàng</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            Đặt hàng
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection 