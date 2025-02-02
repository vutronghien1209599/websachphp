@extends('layouts.admin')

@section('title', 'Chi tiết đánh giá #' . $review->id)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chi tiết đánh giá #{{ $review->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Thông tin người đánh giá</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Họ tên</th>
                                    <td>{{ $review->user->full_name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $review->user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Thời gian</th>
                                    <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Thông tin sách</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Tên sách</th>
                                    <td>
                                        <a href="{{ route('books.show', $review->book) }}" target="_blank">
                                            {{ $review->book->title }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Đánh giá</th>
                                    <td>
                                        <div class="text-warning">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                            @endfor
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Trạng thái</th>
                                    <td>
                                        @if ($review->status === 'pending')
                                            <span class="badge badge-warning">Chờ duyệt</span>
                                        @elseif ($review->status === 'approved')
                                            <span class="badge badge-success">Đã duyệt</span>
                                        @else
                                            <span class="badge badge-danger">Đã từ chối</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Nội dung đánh giá</h5>
                            <div class="card">
                                <div class="card-body">
                                    <h6>Nhận xét</h6>
                                    <p>{{ $review->comment }}</p>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Ưu điểm</h6>
                                            <p>{{ $review->pros ?: 'Không có' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Nhược điểm</h6>
                                            <p>{{ $review->cons ?: 'Không có' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($review->status === 'pending')
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success approve-review" data-id="{{ $review->id }}">
                                            <i class="fas fa-check"></i> Duyệt đánh giá
                                        </button>
                                        <button type="button" class="btn btn-danger reject-review" data-id="{{ $review->id }}">
                                            <i class="fas fa-times"></i> Từ chối đánh giá
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Phản hồi của admin</h5>
                            <div class="card">
                                <div class="card-body">
                                    <form id="response-form" class="mb-4">
                                        <div class="form-group">
                                            <textarea name="content" class="form-control" rows="3" placeholder="Nhập nội dung phản hồi..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary mt-2">
                                            <i class="fas fa-paper-plane"></i> Gửi phản hồi
                                        </button>
                                    </form>

                                    <div id="responses-list">
                                        @forelse ($review->responses as $response)
                                        <div class="card mb-3" id="response-{{ $response->id }}">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <small class="text-muted">
                                                        {{ $response->created_at->format('d/m/Y H:i') }}
                                                    </small>
                                                    <button type="button" class="btn btn-danger btn-sm delete-response" data-id="{{ $response->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                <p class="mb-0">{{ $response->content }}</p>
                                            </div>
                                        </div>
                                        @empty
                                        <p class="text-muted">Chưa có phản hồi nào</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
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

    // Xử lý duyệt đánh giá
    $('.approve-review').click(function() {
        const reviewId = $(this).data('id');
        if (confirm('Bạn có chắc chắn muốn duyệt đánh giá này?')) {
            $.post("{{ route('admin.reviews.approve', $review) }}")
                .done(function(response) {
                    toastr.success(response.message);
                    setTimeout(() => window.location.reload(), 1000);
                })
                .fail(function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Có lỗi xảy ra');
                });
        }
    });

    // Xử lý từ chối đánh giá
    $('.reject-review').click(function() {
        const reviewId = $(this).data('id');
        if (confirm('Bạn có chắc chắn muốn từ chối đánh giá này?')) {
            $.post("{{ route('admin.reviews.reject', $review) }}")
                .done(function(response) {
                    toastr.success(response.message);
                    setTimeout(() => window.location.reload(), 1000);
                })
                .fail(function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Có lỗi xảy ra');
                });
        }
    });

    // Xử lý thêm phản hồi
    $('#response-form').submit(function(e) {
        e.preventDefault();
        const content = $(this).find('textarea[name="content"]').val();
        
        if (!content.trim()) {
            toastr.error('Vui lòng nhập nội dung phản hồi');
            return;
        }

        $.post("{{ route('admin.reviews.respond', $review) }}", { content })
            .done(function(response) {
                toastr.success(response.message);
                
                // Thêm phản hồi mới vào danh sách
                const html = `
                    <div class="card mb-3" id="response-${response.response.id}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">${response.response.created_at}</small>
                                <button type="button" class="btn btn-danger btn-sm delete-response" data-id="${response.response.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <p class="mb-0">${response.response.content}</p>
                        </div>
                    </div>
                `;
                
                if ($('#responses-list p.text-muted').length) {
                    $('#responses-list').html(html);
                } else {
                    $('#responses-list').prepend(html);
                }
                
                // Reset form
                $(this).find('textarea').val('');
            })
            .fail(function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Có lỗi xảy ra');
            });
    });

    // Xử lý xóa phản hồi
    $(document).on('click', '.delete-response', function() {
        const responseId = $(this).data('id');
        if (confirm('Bạn có chắc chắn muốn xóa phản hồi này?')) {
            $.ajax({
                url: `/admin/reviews/responses/${responseId}`,
                type: 'DELETE',
                success: function(response) {
                    toastr.success(response.message);
                    $(`#response-${responseId}`).fadeOut(function() {
                        $(this).remove();
                        if ($('#responses-list .card').length === 0) {
                            $('#responses-list').html('<p class="text-muted">Chưa có phản hồi nào</p>');
                        }
                    });
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