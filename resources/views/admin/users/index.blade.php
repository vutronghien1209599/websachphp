@extends('layouts.admin')

@section('title', 'Quản lý người dùng')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý người dùng</h1>
        <div>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer"></i> In danh sách
            </button>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUser">
                <i class="bi bi-plus-lg"></i> Thêm người dùng
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Bộ lọc -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select class="form-select" id="role-filter">
                        <option value="">Tất cả vai trò</option>
                        <option value="admin">Admin</option>
                        <option value="user">Người dùng</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Tìm kiếm...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên đăng nhập</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Vai trò</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>#{{ $user->id }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->full_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone_number }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge bg-primary">Admin</span>
                                @else
                                    <span class="badge bg-secondary">Người dùng</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#viewUser{{ $user->id }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editUser{{ $user->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if($user->role !== 'admin')
                                    <button type="button" class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteUser{{ $user->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>

                                <!-- Modal xem chi tiết -->
                                <div class="modal fade" id="viewUser{{ $user->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Thông tin người dùng</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Tên đăng nhập:</strong> {{ $user->username }}</p>
                                                <p><strong>Họ tên:</strong> {{ $user->full_name }}</p>
                                                <p><strong>Email:</strong> {{ $user->email }}</p>
                                                <p><strong>Số điện thoại:</strong> {{ $user->phone_number }}</p>
                                                <p><strong>Địa chỉ:</strong> {{ $user->address }}</p>
                                                <p><strong>Vai trò:</strong> 
                                                    @if($user->role === 'admin')
                                                        <span class="badge bg-primary">Admin</span>
                                                    @else
                                                        <span class="badge bg-secondary">Người dùng</span>
                                                    @endif
                                                </p>
                                                <p><strong>Ngày tạo:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal chỉnh sửa -->
                                <div class="modal fade" id="editUser{{ $user->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Chỉnh sửa người dùng</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Tên đăng nhập</label>
                                                        <input type="text" name="username" class="form-control" 
                                                            value="{{ $user->username }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Họ tên</label>
                                                        <input type="text" name="full_name" class="form-control" 
                                                            value="{{ $user->full_name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" name="email" class="form-control" 
                                                            value="{{ $user->email }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Số điện thoại</label>
                                                        <input type="text" name="phone_number" class="form-control" 
                                                            value="{{ $user->phone_number }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Địa chỉ</label>
                                                        <textarea name="address" class="form-control" rows="3">{{ $user->address }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Vai trò</label>
                                                        <select name="role" class="form-select">
                                                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>
                                                                Người dùng
                                                            </option>
                                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>
                                                                Admin
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Mật khẩu mới</label>
                                                        <input type="password" name="password" class="form-control">
                                                        <small class="text-muted">Để trống nếu không muốn thay đổi mật khẩu</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal xóa -->
                                @if($user->role !== 'admin')
                                <div class="modal fade" id="deleteUser{{ $user->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Xác nhận xóa</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Bạn có chắc chắn muốn xóa người dùng này?</p>
                                                <p><strong>Tên đăng nhập:</strong> {{ $user->username }}</p>
                                                <p><strong>Họ tên:</strong> {{ $user->full_name }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Xóa</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    Hiển thị {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} 
                    trên tổng số {{ $users->total() ?? 0 }} người dùng
                </div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal thêm người dùng -->
<div class="modal fade" id="addUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm người dùng mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên đăng nhập</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Họ tên</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone_number" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <textarea name="address" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vai trò</label>
                        <select name="role" class="form-select">
                            <option value="user">Người dùng</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 