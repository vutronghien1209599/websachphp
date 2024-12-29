@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <div>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer"></i> In báo cáo
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Tổng doanh thu Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng doanh thu</div>
                            <div class="h5 mb-0 font-weight-bold">{{ number_format($totalRevenue) }}đ</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Đơn hàng mới Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Đơn hàng mới</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $newOrders }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tổng số sách Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tổng số sách</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $totalBooks ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tổng số người dùng Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Tổng số người dùng</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $totalUsers ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Biểu đồ doanh thu -->
        <div class="col-xl-8 col-lg-7">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Biểu đồ doanh thu</h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-calendar"></i> Thời gian
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">7 ngày qua</a></li>
                            <li><a class="dropdown-item" href="#">30 ngày qua</a></li>
                            <li><a class="dropdown-item" href="#">3 tháng qua</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Biểu đồ phân bố đơn hàng -->
        <div class="col-xl-4 col-lg-5">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Phân bố đơn hàng</h6>
                </div>
                <div class="card-body">
                    <canvas id="orderChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Đơn hàng gần đây -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Đơn hàng gần đây</h6>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">
                        Xem tất cả
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Khách hàng</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->user->username }}</td>
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
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sách bán chạy -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Sách bán chạy</h6>
                    <a href="{{ route('admin.books.index') }}" class="btn btn-sm btn-primary">
                        Xem tất cả
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Sách</th>
                                    <th>Đã bán</th>
                                    <th>Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topBooks as $book)
                                    <tr>
                                        <td>{{ $book->title }}</td>
                                        <td>{{ $book->total_sold }}</td>
                                        <td>{{ number_format($book->revenue) }}đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Biểu đồ doanh thu
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
        datasets: [{
            label: 'Doanh thu',
            data: [65, 59, 80, 81, 56, 55, 40],
            fill: false,
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Biểu đồ phân bố đơn hàng
const orderCtx = document.getElementById('orderChart').getContext('2d');
new Chart(orderCtx, {
    type: 'doughnut',
    data: {
        labels: ['Chờ xác nhận', 'Đã xác nhận', 'Đang giao', 'Đã giao', 'Đã hủy'],
        datasets: [{
            data: [12, 19, 3, 5, 2],
            backgroundColor: [
                '#f6c23e',
                '#36b9cc',
                '#4e73df',
                '#1cc88a',
                '#e74a3b'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>
@endpush
@endsection 