@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->id)

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2>Chi tiết đơn hàng #{{ $order->id }}</h2>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5>Thông tin đơn hàng</h5>
                        <p>Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        <p>Trạng thái: 
                            @switch($order->status)
                                @case('pending')
                                    <span class="badge bg-warning">Chờ xác nhận</span>
                                    @break
                                @case('confirmed')
                                    <span class="badge bg-info">Đã xác nhận</span>
                                    @break
                                @case('shipping')
                                    <span class="badge bg-primary">Đang giao</span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-success">Đã giao</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge bg-danger">Đã hủy</span>
                                    @break
                            @endswitch
                        </p>
                        <p>Phương thức thanh toán: 
                            {{ $order->payment_method === 'cod' ? 'Thanh toán khi nhận hàng' : 'Chuyển khoản ngân hàng' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h5>Địa chỉ giao hàng</h5>
                        <p>{{ $order->shipping_address }}</p>
                    </div>
                </div>

                <h5>Chi tiết sản phẩm</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Tổng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderDetails as $detail)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('storage/books/'.$detail->book->image) }}" 
                                                 alt="{{ $detail->book->title }}" 
                                                 style="width: 50px; margin-right: 10px;">
                                            <div>
                                                <h6 class="mb-0">{{ $detail->book->title }}</h6>
                                                <small class="text-muted">{{ $detail->book->author }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ number_format($detail->price) }}đ</td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>{{ number_format($detail->price * $detail->quantity) }}đ</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Tạm tính:</strong></td>
                                <td>{{ number_format($order->total_amount - 30000) }}đ</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Phí vận chuyển:</strong></td>
                                <td>30,000đ</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                <td><strong>{{ number_format($order->total_amount) }}đ</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 