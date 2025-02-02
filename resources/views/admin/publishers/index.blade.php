@extends('layouts.admin')

@section('title', 'Quản lý nhà xuất bản')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<style>
.publisher-logo {
    width: 50px;
    height: 50px;
    object-fit: contain;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý nhà xuất bản</h2>
        <a href="{{ route('admin.publishers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Thêm nhà xuất bản mới
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Form tìm kiếm -->
            <form action="{{ route('admin.publishers.index') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Tìm theo tên nhà xuất bản" 
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
                            <th>Logo</th>
                            <th>Tên nhà xuất bản</th>
                            <th>Email</th>
                            <th>Số sách</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($publishers as $publisher)
                            <tr>
                                <td>{{ $publisher->id }}</td>
                                <td>
                                    @if($publisher->logo)
                                        <img src="{{ Storage::url('publishers/' . $publisher->logo) }}" 
                                             alt="{{ $publisher->name }}" 
                                             class="publisher-logo">
                                    @else
                                        <div class="publisher-logo bg-light d-flex align-items-center justify-content-center">
                                            <i class="bi bi-building text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $publisher->name }}</td>
                                <td>{{ $publisher->email }}</td>
                                <td>{{ $publisher->books_count }}</td>
                                <td>
                                    @if($publisher->is_active)
                                        <span class="badge bg-success">Hoạt động</span>
                                    @else
                                        <span class="badge bg-danger">Không hoạt động</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.publishers.edit', $publisher) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.publishers.destroy', $publisher) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Bạn có chắc muốn xóa nhà xuất bản này?')">
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
                {{ $publishers->links() }}
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