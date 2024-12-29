@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2>Đơn hàng của tôi</h2>
        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ number_format($order->total_amount) }}đ</td>
                                <td>
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
                                </td>
                                <td>
                                    <a href="{{ route('orders.show', $order) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        Chi tiết
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $orders->links() }}
            </div>
        @else
            <div class="alert alert-info">
                Bạn chưa có đơn hàng nào. 
                <a href="{{ route('books.index') }}" class="alert-link">Mua sắm ngay</a>
            </div>
        @endif
    </div>
</div>
@endsection 