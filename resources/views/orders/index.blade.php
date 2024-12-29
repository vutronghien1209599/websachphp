@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-bag-check"></i> Đơn hàng của tôi
                </h2>
                <a href="{{ route('books.index') }}" class="btn btn-primary">
                    <i class="bi bi-cart-plus"></i> Mua sắm thêm
                </a>
            </div>

            @if($orders->count() > 0)
                <div class="row">
                    @foreach($orders as $order)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom-0 pt-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <i class="bi bi-receipt"></i> Đơn hàng #{{ $order->id }}
                                        </h5>
                                        @switch($order->status)
                                            @case('pending')
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-clock"></i> Chờ xác nhận
                                                </span>
                                                @break
                                            @case('confirmed')
                                                <span class="badge bg-info">
                                                    <i class="bi bi-check-circle"></i> Đã xác nhận
                                                </span>
                                                @break
                                            @case('shipping')
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-truck"></i> Đang giao
                                                </span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check2-all"></i> Đã giao
                                                </span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle"></i> Đã hủy
                                                </span>
                                                @break
                                        @endswitch
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between text-muted mb-2">
                                            <span>
                                                <i class="bi bi-calendar3"></i> 
                                                {{ $order->created_at->format('d/m/Y H:i') }}
                                            </span>
                                            <span>
                                                <i class="bi bi-credit-card"></i>
                                                {{ $order->payment_method === 'cod' ? 'Thanh toán khi nhận hàng' : 'VNPay' }}
                                            </span>
                                        </div>
                                        <div class="text-muted mb-2">
                                            <i class="bi bi-geo-alt"></i> 
                                            {{ Str::limit($order->shipping_address, 50) }}
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">Tổng tiền</small>
                                            <h5 class="mb-0 text-primary">{{ number_format($order->total_amount) }}đ</h5>
                                        </div>
                                        <a href="{{ route('orders.show', $order) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i> Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-bag-x display-1 text-muted mb-4"></i>
                    <h4>Bạn chưa có đơn hàng nào</h4>
                    <p class="text-muted">Hãy khám phá những cuốn sách hay tại cửa hàng của chúng tôi</p>
                    <a href="{{ route('books.index') }}" class="btn btn-primary">
                        <i class="bi bi-cart-plus"></i> Mua sắm ngay
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 