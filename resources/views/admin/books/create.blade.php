@extends('layouts.admin')

@section('title', 'Thêm sách mới')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<link href="{{ asset('css/select2-custom.css') }}" rel="stylesheet" />
<style>
/* Select2 Container */
.select2-container--bootstrap-5 {
    width: 100% !important;
}

/* Selection box */
.select2-container--bootstrap-5 .select2-selection {
    min-height: 38px;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.25rem 0.5rem;
    background-color: #fff;
}

/* Multiple selections */
.select2-container--bootstrap-5 .select2-selection--multiple {
    padding: 3px 8px;
}

.select2-container--bootstrap-5 .select2-selection--multiple .select2-search__field {
    margin: 5px 0;
    padding: 0;
    height: 25px;
}

/* Selected items */
.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
    background-color: var(--primary-color);
    border: none;
    color: #fff;
    padding: 2px 8px;
    margin: 4px 4px;
    border-radius: 3px;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
}

/* Remove button on selected items */
.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
    color: #fff;
    margin-right: 5px;
    font-size: 1rem;
    display: flex;
    align-items: center;
    padding: 0 3px;
    border-radius: 3px;
}

.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover {
    background-color: rgba(255, 255, 255, 0.2);
    color: #fff;
}

/* Dropdown */
.select2-container--bootstrap-5 .select2-dropdown {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    background-color: #fff;
    margin-top: 2px;
}

/* Search box in dropdown */
.select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
}

/* Options in dropdown */
.select2-container--bootstrap-5 .select2-results__option {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
    background-color: var(--primary-color);
    color: #fff;
}

/* Add new author button */
.add-new-author {
    border-top: 1px solid #dee2e6;
    margin-top: 0.5rem;
    padding: 0.75rem !important;
}

.add-new-author button {
    transition: all 0.2s;
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

.add-new-author button:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Focus state */
.select2-container--bootstrap-5.select2-container--focus .select2-selection {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Error state */
.is-invalid + .select2-container--bootstrap-5 .select2-selection {
    border-color: #dc3545;
}

.is-invalid + .select2-container--bootstrap-5.select2-container--focus .select2-selection {
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Thêm sách mới</h2>
            <div>
                <a href="{{ route('admin.authors.index') }}" class="btn btn-outline-primary me-2">
                    <i class="bi bi-person-lines-fill"></i> Quản lý tác giả
                </a>
                <a href="{{ route('admin.publishers.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-building"></i> Quản lý NXB
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Thông tin cơ bản -->
                    <div class="col-md-8">
                        <h4 class="mb-3">Thông tin cơ bản</h4>
                        <div class="mb-3">
                            <label class="form-label">Tên sách</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tác giả</label>
                            <select name="author_ids[]" class="form-select @error('author_ids') is-invalid @enderror" 
                                    multiple size="5" required>
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}" 
                                            {{ in_array($author->id, old('author_ids', [])) ? 'selected' : '' }}>
                                        {{ $author->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Giữ phím Ctrl (Windows) hoặc Command (Mac) để chọn nhiều tác giả</div>
                            @error('author_ids')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" rows="5" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Danh mục</label>
                                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                        <option value="">Chọn danh mục</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nhà xuất bản</label>
                                    <select name="publisher_id" class="form-select @error('publisher_id') is-invalid @enderror" required>
                                        <option value="">Chọn nhà xuất bản</option>
                                        @foreach($publishers as $publisher)
                                            <option value="{{ $publisher->id }}" 
                                                    {{ old('publisher_id') == $publisher->id ? 'selected' : '' }}>
                                                {{ $publisher->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('publisher_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Ngôn ngữ gốc</label>
                                    <input type="text" name="original_language" 
                                           class="form-control @error('original_language') is-invalid @enderror"
                                           value="{{ old('original_language') }}" required>
                                    @error('original_language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                            Hoạt động
                                        </option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                            Không hoạt động
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin phiên bản -->
                    <div class="col-md-4">
                        <h4 class="mb-3">Thông tin phiên bản</h4>
                        <div class="mb-3">
                            <label class="form-label">Ảnh bìa</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" 
                                   accept="image/*" required>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Số phiên bản</label>
                                    <input type="text" name="edition_number" 
                                           class="form-control @error('edition_number') is-invalid @enderror"
                                           value="{{ old('edition_number') }}" required>
                                    @error('edition_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Số lần in</label>
                                    <input type="number" name="reprint_number" 
                                           class="form-control @error('reprint_number') is-invalid @enderror"
                                           value="{{ old('reprint_number', 1) }}" min="1" required>
                                    @error('reprint_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ngày xuất bản</label>
                            <input type="date" name="publication_date" 
                                   class="form-control @error('publication_date') is-invalid @enderror"
                                   value="{{ old('publication_date') }}" required>
                            @error('publication_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ISBN</label>
                            <input type="text" name="isbn" 
                                   class="form-control @error('isbn') is-invalid @enderror"
                                   value="{{ old('isbn') }}" required>
                            @error('isbn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Số trang</label>
                                    <input type="number" name="pages" 
                                           class="form-control @error('pages') is-invalid @enderror"
                                           value="{{ old('pages') }}" min="1" required>
                                    @error('pages')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Định dạng</label>
                                    <input type="text" name="format" 
                                           class="form-control @error('format') is-invalid @enderror"
                                           value="{{ old('format') }}" required>
                                    @error('format')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kích thước</label>
                                    <input type="text" name="dimensions" 
                                           class="form-control @error('dimensions') is-invalid @enderror"
                                           value="{{ old('dimensions') }}" placeholder="VD: 14 x 20.5 cm">
                                    @error('dimensions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Trọng lượng (gram)</label>
                                    <input type="number" name="weight" 
                                           class="form-control @error('weight') is-invalid @enderror"
                                           value="{{ old('weight') }}" min="0" step="0.1">
                                    @error('weight')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Giá bán</label>
                                    <input type="number" name="price" 
                                           class="form-control @error('price') is-invalid @enderror"
                                           value="{{ old('price') }}" min="0" step="1000" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Số lượng</label>
                                    <input type="number" name="quantity" 
                                           class="form-control @error('quantity') is-invalid @enderror"
                                           value="{{ old('quantity') }}" min="0" required>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <a href="{{ route('admin.books.index') }}" class="btn btn-secondary me-2">Hủy</a>
                    <button type="submit" class="btn btn-primary">Thêm sách</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal tạo tác giả mới -->
<div class="modal fade" id="createAuthorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm tác giả mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createAuthorForm">
                    <div class="mb-3">
                        <label class="form-label">Tên tác giả</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tiểu sử</label>
                        <textarea name="biography" class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="saveAuthorBtn">Lưu</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Khởi tạo Select2
    $('.select2-authors').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Chọn tác giả',
        allowClear: true,
        tags: false,
        language: {
            noResults: function() {
                return "Không tìm thấy tác giả";
            },
            searching: function() {
                return "Đang tìm...";
            }
        },
        ajax: {
            url: '{{ route("api.authors.search") }}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            processResults: function(data) {
                return {
                    results: data.data.map(function(author) {
                        return {
                            id: author.id,
                            text: author.name
                        };
                    }),
                    pagination: {
                        more: data.current_page < data.last_page
                    }
                };
            },
            cache: true
        }
    }).on('select2:open', function() {
        setTimeout(function() {
            // Thêm nút "Thêm tác giả mới" vào dropdown
            if (!$('.select2-results__options').find('.add-new-author').length) {
                $('.select2-results__options').append(
                    '<div class="select2-results__option add-new-author">' +
                    '<button class="btn btn-primary btn-sm w-100" onclick="openCreateAuthorModal()">' +
                    '<i class="bi bi-plus-lg me-1"></i>Thêm tác giả mới' +
                    '</button>' +
                    '</div>'
                );
            }
        }, 0);
    });

    // Xử lý thêm tác giả mới
    $('#saveAuthorBtn').click(function() {
        const formData = new FormData(document.getElementById('createAuthorForm'));
        
        fetch('{{ route("api.authors.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(author => {
            // Thêm tác giả mới vào Select2
            const newOption = new Option(author.name, author.id, true, true);
            $('.select2-authors').append(newOption).trigger('change');
            
            // Đóng modal
            $('#createAuthorModal').modal('hide');
            document.getElementById('createAuthorForm').reset();
        })
        .catch(error => {
            alert('Có lỗi xảy ra. Vui lòng thử lại.');
        });
    });
});

function openCreateAuthorModal() {
    $('.select2-container').addClass('select2-container--closed');
    $('#createAuthorModal').modal('show');
}
</script>
@endpush 