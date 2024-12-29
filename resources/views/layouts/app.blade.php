<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Book Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <style>
    :root {
        --primary-color: #2c3e50;
        --secondary-color: #3498db;
        --accent-color: #e74c3c;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .navbar {
        background: var(--primary-color) !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 1rem 0;
    }

    .navbar-brand {
        font-size: 1.5rem;
        font-weight: bold;
        color: white !important;
    }

    .nav-link {
        color: rgba(255,255,255,0.9) !important;
        font-weight: 500;
        padding: 0.5rem 1rem !important;
        transition: all 0.3s ease;
    }

    .nav-link:hover {
        color: var(--secondary-color) !important;
        transform: translateY(-2px);
    }

    .dropdown-menu {
        border: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .dropdown-item {
        padding: 0.7rem 1.5rem;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: var(--secondary-color);
        color: white;
    }

    .badge {
        position: absolute;
        top: 0;
        right: 0;
        transform: translate(50%, -50%);
    }

    .cart-link {
        position: relative;
        padding-right: 1.5rem !important;
    }

    main {
        flex: 1;
        background-color: #f8f9fa;
        padding: 2rem 0;
    }

    .footer {
        background: var(--primary-color);
        color: white;
        padding: 3rem 0;
    }

    .footer a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .footer a:hover {
        color: var(--secondary-color);
    }

    .alert {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .pagination {
        margin-bottom: 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .page-link {
        padding: 0.5rem 0.75rem;
        color: var(--primary-color);
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }

    .page-link:hover {
        background-color: var(--secondary-color);
        color: white;
        border-color: var(--secondary-color);
    }

    .page-item.active .page-link {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-book"></i> Book Store
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-grid"></i> Danh mục
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('books.index') }}?category=fiction">
                                <i class="bi bi-book"></i> Sách văn học
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('books.index') }}?category=education">
                                <i class="bi bi-journal-text"></i> Sách giáo khoa
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('books.index') }}?category=children">
                                <i class="bi bi-emoji-smile"></i> Sách thiếu nhi
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('books.index') }}?category=foreign">
                                <i class="bi bi-translate"></i> Sách ngoại ngữ
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('books.index') }}">
                            <i class="bi bi-search"></i> Tìm sách
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-star"></i> Sách bán chạy
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-percent"></i> Khuyến mãi
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="bi bi-person-plus"></i> Đăng ký
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link cart-link" href="{{ route('cart.index') }}">
                                <i class="bi bi-cart3"></i> Giỏ hàng 
                                @if(Auth::user()->cart->count() > 0)
                                    <span class="badge bg-danger">{{ Auth::user()->cart->count() }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->username }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(Auth::user()->role === 'admin')
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-speedometer2"></i> Quản trị
                                    </a></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="bi bi-person"></i> Thông tin cá nhân
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('orders.index') }}">
                                    <i class="bi bi-bag"></i> Đơn hàng của tôi
                                </a></li>
                                <li><a class="dropdown-item" href="#">
                                    <i class="bi bi-heart"></i> Sách yêu thích
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="footer mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="bi bi-book"></i> Book Store</h5>
                    <p><i class="bi bi-geo-alt-fill"></i> 123 Đường ABC, Quận XYZ, TP.HCM</p>
                    <p><i class="bi bi-telephone-fill"></i> (028) 1234 5678</p>
                    <p><i class="bi bi-envelope-fill"></i> contact@bookstore.com</p>
                </div>
                <div class="col-md-4">
                    <h5>Thông tin</h5>
                    <ul class="list-unstyled">
                        <li><a href="#"><i class="bi bi-info-circle"></i> Về chúng tôi</a></li>
                        <li><a href="#"><i class="bi bi-shield-check"></i> Chính sách bảo mật</a></li>
                        <li><a href="#"><i class="bi bi-file-text"></i> Điều khoản sử dụng</a></li>
                        <li><a href="#"><i class="bi bi-truck"></i> Chính sách vận chuyển</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Theo dõi chúng tôi</h5>
                    <div class="social-links">
                        <a href="#" class="me-3"><i class="bi bi-facebook fs-4"></i></a>
                        <a href="#" class="me-3"><i class="bi bi-instagram fs-4"></i></a>
                        <a href="#" class="me-3"><i class="bi bi-twitter fs-4"></i></a>
                        <a href="#"><i class="bi bi-youtube fs-4"></i></a>
                    </div>
                    <h5 class="mt-4">Đăng ký nhận tin</h5>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Email của bạn">
                        <button class="btn btn-primary" type="button">Đăng ký</button>
                    </div>
                </div>
            </div>
            <hr class="mt-4">
            <div class="text-center">
                <p class="mb-0">&copy; 2024 Book Store. Tất cả quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 