@extends('layouts.admin')

@section('title', 'Chỉnh sửa nhà xuất bản')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Chỉnh sửa nhà xuất bản: {{ $publisher->name }}</h2>
            <a href="{{ route('admin.publishers.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.publishers.update', $publisher) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Tên nhà xuất bản</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $publisher->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" rows="5" 
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description', $publisher->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" 
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', $publisher->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="text" name="phone" 
                                           class="form-control @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', $publisher->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" name="address" 
                                   class="form-control @error('address') is-invalid @enderror"
                                   value="{{ old('address', $publisher->address) }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Website</label>
                            <input type="url" name="website" 
                                   class="form-control @error('website') is-invalid @enderror"
                                   value="{{ old('website', $publisher->website) }}">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                                <option value="1" {{ old('is_active', $publisher->is_active) ? 'selected' : '' }}>Hoạt động</option>
                                <option value="0" {{ old('is_active', $publisher->is_active) ? '' : 'selected' }}>Không hoạt động</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Số sách đã xuất bản</label>
                            <input type="text" class="form-control" value="{{ $publisher->books_count }}" disabled>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Logo hiện tại</label>
                            @if($publisher->logo)
                                <img src="{{ Storage::url('publishers/' . $publisher->logo) }}" 
                                     alt="{{ $publisher->name }}" 
                                     class="d-block mb-2" style="max-width: 200px;">
                            @else
                                <div class="alert alert-info">Chưa có logo</div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Thay đổi logo</label>
                            <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" 
                                   accept="image/*">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <a href="{{ route('admin.publishers.index') }}" class="btn btn-secondary me-2">Hủy</a>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 