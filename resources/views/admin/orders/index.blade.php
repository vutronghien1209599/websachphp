@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý đơn hàng</h1>
        <div>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer"></i> In danh sách
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Bộ lọc -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select class="form-select" id="status-filter">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending">Chờ xác nhận</option>
                        <option value="confirmed">Đã xác nhận</option>
                        <option value="shipping">Đang giao</option>
                        <option value="completed">Đã giao</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="date-filter">
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Tìm kiếm...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Phương thức</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>
                                <div>{{ $order->user->full_name }}</div>
                                <small class="text-muted">{{ $order->user->email }}</small>
                            </td>
                            <td>{{ number_format($order->total_amount) }}đ</td>
                            <td>{{ $order->payment_method }}</td>
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
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" 
                                        class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#updateStatus{{ $order->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </div>

                                <!-- Modal cập nhật trạng thái -->
                                <div class="modal fade" id="updateStatus{{ $order->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Cập nhật trạng thái đơn hàng #{{ $order->id }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
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
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    Hiển thị {{ $orders->firstItem() ?? 0 }}-{{ $orders->lastItem() ?? 0 }} 
                    trên tổng số {{ $orders->total() ?? 0 }} đơn hàng
                </div>
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 