@extends('layouts.admin')

@section('title', 'Quản lý tác giả')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<style>
.modal-header {
    background: var(--primary-color);
    color: white;
}
.modal-title {
    font-weight: 600;
}
.btn-close {
    filter: brightness(0) invert(1);
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý tác giả</h2>
        <a href="{{ route('admin.authors.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Thêm tác giả mới
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Form tìm kiếm -->
            <form action="{{ route('admin.authors.index') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Tìm theo tên tác giả" 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Tìm kiếm
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên tác giả</th>
                            <th>Số sách</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($authors as $author)
                            <tr>
                                <td>{{ $author->id }}</td>
                                <td>{{ $author->name }}</td>
                                <td>{{ $author->books_count }}</td>
                                <td>
                                    @if($author->is_active)
                                        <span class="badge bg-success">Hoạt động</span>
                                    @else
                                        <span class="badge bg-danger">Không hoạt động</span>
                                    @endif
                                </td>
                                <td>{{ $author->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.authors.edit', $author) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.authors.destroy', $author) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Bạn có chắc muốn xóa tác giả này?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $authors->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
// Cấu hình toastr
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: 3000
};

// Hiển thị thông báo nếu có
@if(session('success'))
    toastr.success('{{ session('success') }}');
@endif

@if(session('error'))
    toastr.error('{{ session('error') }}');
@endif
</script>
@endpush 