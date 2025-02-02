@extends('layouts.admin')

@section('title', 'Quản lý đánh giá')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách đánh giá</h3>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tìm kiếm sách</label>
                                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nhập tên sách...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <select name="status" class="form-control">
                                        <option value="">Tất cả</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Tìm kiếm
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="15%">Người đánh giá</th>
                                    <th width="25%">Sách</th>
                                    <th width="10%">Đánh giá</th>
                                    <th width="15%">Thời gian</th>
                                    <th width="10%">Trạng thái</th>
                                    <th width="20%">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reviews as $review)
                                <tr>
                                    <td>{{ $review->id }}</td>
                                    <td>
                                        {{ $review->user->name }}<br>
                                        <small class="text-muted">{{ $review->user->email }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('books.show', $review->book) }}" target="_blank">
                                            {{ $review->book->title }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="text-warning">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                            @endfor
                                        </div>
                                    </td>
                                    <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if ($review->status === 'pending')
                                            <span class="badge badge-warning bg-warning">Chờ duyệt</span>
                                        @elseif ($review->status === 'approved')
                                            <span class="badge badge-success bg-success">Đã duyệt</span>
                                        @else
                                            <span class="badge badge-danger bg-danger">Đã từ chối</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.reviews.show', $review) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>
                                        @if ($review->status === 'pending')
                                        <button type="button" class="btn btn-success btn-sm approve-review" data-id="{{ $review->id }}">
                                            <i class="fas fa-check"></i> Duyệt
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm reject-review" data-id="{{ $review->id }}">
                                            <i class="fas fa-times"></i> Từ chối
                                        </button>
                                        @endif
                                        <button type="button" class="btn btn-danger btn-sm delete-review" data-id="{{ $review->id }}">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Không có đánh giá nào</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $reviews->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.approve-review').click(function() {
        const reviewId = $(this).data('id');
        if (confirm('Bạn có chắc chắn muốn duyệt đánh giá này?')) {
            $.post(`/admin/reviews/${reviewId}/approve`)
                .done(function(response) {
                    toastr.success(response.message);
                    setTimeout(() => window.location.reload(), 1000);
                })
                .fail(function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Có lỗi xảy ra');
                });
        }
    });

    $('.reject-review').click(function() {
        const reviewId = $(this).data('id');
        if (confirm('Bạn có chắc chắn muốn từ chối đánh giá này?')) {
            $.post(`/admin/reviews/${reviewId}/reject`)
                .done(function(response) {
                    toastr.success(response.message);
                    setTimeout(() => window.location.reload(), 1000);
                })
                .fail(function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Có lỗi xảy ra');
                });
        }
    });

    $('.delete-review').click(function() {
        const reviewId = $(this).data('id');
        if (confirm('Bạn có chắc chắn muốn xóa đánh giá này?')) {
            $.ajax({
                url: `/admin/reviews/${reviewId}`,
                type: 'DELETE',
                success: function(response) {
                    toastr.success(response.message);
                    setTimeout(() => window.location.reload(), 1000);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Có lỗi xảy ra');
                }
            });
        }
    });
});
</script>
@endpush 