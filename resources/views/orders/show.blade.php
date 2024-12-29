@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->id)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-receipt"></i> Chi tiết đơn hàng #{{ $order->id }}
                </h2>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="order-info">
                                <h5 class="card-title">
                                    <i class="bi bi-info-circle"></i> Thông tin đơn hàng
                                </h5>
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item border-0 px-0">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Trạng thái</span>
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
                                    <div class="list-group-item border-0 px-0">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Ngày đặt</span>
                                            <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                    <div class="list-group-item border-0 px-0">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Phương thức thanh toán</span>
                                            <span>
                                                @if($order->payment_method === 'cod')
                                                    <i class="bi bi-cash"></i> Thanh toán khi nhận hàng
                                                @else
                                                    <i class="bi bi-credit-card"></i> VNPay
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="shipping-info">
                                <h5 class="card-title">
                                    <i class="bi bi-truck"></i> Thông tin giao hàng
                                </h5>
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item border-0 px-0">
                                        <span class="text-muted">Địa chỉ giao hàng</span>
                                        <p class="mb-0 mt-1">{{ $order->shipping_address }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="bi bi-box"></i> Chi tiết sản phẩm
                    </h5>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Sản phẩm</th>
                                    <th scope="col" class="text-end">Giá</th>
                                    <th scope="col" class="text-center">Số lượng</th>
                                    <th scope="col" class="text-end">Tổng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td style="min-width: 300px;">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('storage/books/'.$item->book->image) }}" 
                                                     alt="{{ $item->book->title }}" 
                                                     class="rounded"
                                                     style="width: 60px; height: 80px; object-fit: cover;">
                                                <div class="ms-3">
                                                    <h6 class="mb-1">{{ $item->book->title }}</h6>
                                                    <small class="text-muted">{{ $item->book->author }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">{{ number_format($item->price) }}đ</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">{{ number_format($item->price * $item->quantity) }}đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end">Tạm tính:</td>
                                    <td class="text-end">{{ number_format($order->total_amount - 30000) }}đ</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">Phí vận chuyển:</td>
                                    <td class="text-end">30,000đ</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                    <td class="text-end"><strong>{{ number_format($order->total_amount) }}đ</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 