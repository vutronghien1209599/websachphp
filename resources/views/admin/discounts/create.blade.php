@extends('layouts.admin')

@section('title', 'Thêm Voucher Mới')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thêm Voucher Mới</h1>
        <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.discounts.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tên voucher <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mã voucher <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                   name="code" value="{{ old('code') }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Mã voucher phải là duy nhất và không có dấu cách</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Loại giảm giá <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    name="type" required>
                                <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>
                                    Giảm số tiền cố định
                                </option>
                                <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>
                                    Giảm theo phần trăm
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Giá trị giảm <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('value') is-invalid @enderror" 
                                       name="value" value="{{ old('value') }}" required step="0.01">
                                <span class="input-group-text type-suffix">đ</span>
                            </div>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Giá trị đơn hàng tối thiểu <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('min_order_amount') is-invalid @enderror" 
                                       name="min_order_amount" value="{{ old('min_order_amount', 0) }}" required>
                                <span class="input-group-text">đ</span>
                            </div>
                            @error('min_order_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Giảm tối đa</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('max_discount_amount') is-invalid @enderror" 
                                       name="max_discount_amount" value="{{ old('max_discount_amount') }}">
                                <span class="input-group-text">đ</span>
                            </div>
                            @error('max_discount_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Chỉ áp dụng cho giảm giá theo phần trăm</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Giới hạn số lần sử dụng</label>
                            <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" 
                                   name="usage_limit" value="{{ old('usage_limit') }}">
                            @error('usage_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Để trống nếu không giới hạn</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Thời gian bắt đầu</label>
                            <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                   name="start_date" value="{{ old('start_date') }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Thời gian kết thúc</label>
                            <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                   name="end_date" value="{{ old('end_date') }}">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                       name="is_active" value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label">Kích hoạt voucher</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Tạo voucher
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.querySelector('select[name="type"]');
    const typeSuffix = document.querySelector('.type-suffix');
    const maxDiscountGroup = document.querySelector('input[name="max_discount_amount"]').closest('.mb-3');

    function updateType() {
        const type = typeSelect.value;
        typeSuffix.textContent = type === 'fixed' ? 'đ' : '%';
        maxDiscountGroup.style.display = type === 'fixed' ? 'none' : 'block';
    }

    typeSelect.addEventListener('change', updateType);
    updateType();
});
</script>
@endpush 