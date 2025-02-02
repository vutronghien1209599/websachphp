@extends('layouts.admin')

@section('title', 'Thêm tác giả mới')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Thêm tác giả mới</h2>
            <a href="{{ route('admin.authors.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.authors.store') }}" method="POST" id="createAuthorForm">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Tên tác giả</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tiểu sử</label>
                            <textarea name="biography" rows="5" 
                                      class="form-control @error('biography') is-invalid @enderror">{{ old('biography') }}</textarea>
                            @error('biography')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Không hoạt động</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <a href="{{ route('admin.authors.index') }}" class="btn btn-secondary me-2">Hủy</a>
                    <button type="submit" class="btn btn-primary">Thêm tác giả</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('createAuthorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("admin.authors.index") }}';
        }
    })
    .catch(error => {
        alert('Có lỗi xảy ra. Vui lòng thử lại.');
    });
});
</script>
@endpush 