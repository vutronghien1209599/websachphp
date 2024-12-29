@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->id)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết đơn hàng #{{ $order->id }}</h1>
        <div>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer"></i> In đơn hàng
            </button>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Thông tin đơn hàng -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin đơn hàng</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $item->book->image }}" alt="{{ $item->book->title }}" 
                                                class="img-thumbnail me-3" style="width: 60px;">
                                            <div>
                                                <div>{{ $item->book->title }}</div>
                                                <small class="text-muted">{{ $item->book->author }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ number_format($item->price) }}đ</td>
                                    <td class="text-end">{{ number_format($item->price * $item->quantity) }}đ</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Tổng tiền:</strong></td>
                                    <td class="text-end"><strong>{{ number_format($order->total_amount) }}đ</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Lịch sử đơn hàng -->
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch sử đơn hàng</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($order->history as $history)
                        <div class="timeline-item">
                            <div class="timeline-date">{{ $history->created_at->format('d/m/Y H:i') }}</div>
                            <div class="timeline-content">
                                <div class="font-weight-bold">{{ $history->status }}</div>
                                <div class="text-muted">{{ $history->note }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Thông tin khách hàng -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin khách hàng</h6>
                </div>
                <div class="card-body">
                    <p><strong>Họ tên:</strong> {{ $order->user->full_name }}</p>
                    <p><strong>Email:</strong> {{ $order->user->email }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $order->user->phone_number }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $order->user->address }}</p>
                </div>
            </div>

            <!-- Thông tin thanh toán -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin thanh toán</h6>
                </div>
                <div class="card-body">
                    <p><strong>Phương thức:</strong> {{ $order->payment_method }}</p>
                    <p><strong>Trạng thái:</strong> 
                        @switch($order->payment_status)
                            @case('pending')
                                <span class="badge bg-warning">Chưa thanh toán</span>
                                @break
                            @case('completed')
                                <span class="badge bg-success">Đã thanh toán</span>
                                @break
                            @case('failed')
                                <span class="badge bg-danger">Thanh toán thất bại</span>
                                @break
                        @endswitch
                    </p>
                    @if($order->payment_method == 'vnpay')
                        <p><strong>Mã giao dịch:</strong> {{ $order->transaction_id }}</p>
                    @endif
                </div>
            </div>

            <!-- Cập nhật trạng thái -->
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Cập nhật trạng thái</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>
                                    Chờ xác nhận
                                </option>
                                <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>
                                    Đã xác nhận
                                </option>
                                <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }}>
                                    Đang giao
                                </option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>
                                    Đã giao
                                </option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>
                                    Đã hủy
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="note" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save"></i> Cập nhật
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    position: relative;
    padding-left: 40px;
    margin-bottom: 20px;
}

.timeline-item:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: -20px;
    width: 2px;
    background: #e3e6f0;
}

.timeline-item:last-child:before {
    bottom: 0;
}

.timeline-item:after {
    content: '';
    position: absolute;
    left: -4px;
    top: 8px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #4e73df;
}

.timeline-date {
    font-size: 0.875rem;
    color: #858796;
    margin-bottom: 5px;
}

.timeline-content {
    background: #f8f9fc;
    padding: 15px;
    border-radius: 5px;
}
</style>
@endsection 