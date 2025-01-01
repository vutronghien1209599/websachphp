@extends('layouts.admin')

@section('title', 'Quản Lý Voucher')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản Lý Voucher</h1>
        <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Thêm voucher mới
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mã</th>
                            <th>Tên</th>
                            <th>Loại</th>
                            <th>Giá trị</th>
                            <th>Đơn tối thiểu</th>
                            <th>Giảm tối đa</th>
                            <th>Giới hạn</th>
                            <th>Đã dùng</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($discounts as $discount)
                        <tr>
                            <td>{{ $discount->id }}</td>
                            <td>
                                <code>{{ $discount->code }}</code>
                            </td>
                            <td>{{ $discount->name }}</td>
                            <td>
                                @if ($discount->type === 'fixed')
                                    <span class="badge bg-primary">Giảm cố định</span>
                                @else
                                    <span class="badge bg-info">Giảm phần trăm</span>
                                @endif
                            </td>
                            <td>
                                @if ($discount->type === 'fixed')
                                    {{ number_format($discount->value) }}đ
                                @else
                                    {{ number_format($discount->value) }}%
                                @endif
                            </td>
                            <td>{{ number_format($discount->min_order_amount) }}đ</td>
                            <td>
                                @if ($discount->max_discount_amount)
                                    {{ number_format($discount->max_discount_amount) }}đ
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($discount->usage_limit)
                                    {{ number_format($discount->usage_limit) }}
                                @else
                                    Không giới hạn
                                @endif
                            </td>
                            <td>{{ number_format($discount->used_count) }}</td>
                            <td>
                                @if ($discount->start_date && $discount->end_date)
                                    {{ date('d/m/Y H:i', strtotime($discount->start_date)) }} -<br>
                                    {{ date('d/m/Y H:i', strtotime($discount->end_date)) }}
                                @else
                                    Không giới hạn
                                @endif
                            </td>
                            <td>
                                @if ($discount->is_active)
                                    <span class="badge bg-success">Đang hoạt động</span>
                                @else
                                    <span class="badge bg-danger">Đã tắt</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.discounts.edit', $discount->id) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="confirmDelete({{ $discount->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $discount->id }}" 
                                      action="{{ route('admin.discounts.destroy', $discount->id) }}" 
                                      method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center">Chưa có voucher nào</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $discounts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(id) {
    if (confirm('Bạn có chắc chắn muốn xóa voucher này?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush 