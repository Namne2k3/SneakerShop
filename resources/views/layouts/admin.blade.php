<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Admin Dashboard for Sneaker Shop" />
    <meta name="author" content="" />
    <title>@yield('title', 'Dashboard - Sneaker Shop Admin')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            overflow-x: hidden;
            background-color: #f8f9fa;
        }
        
        .sb-sidenav {
            background-color: #212529;
            height: 100%;
        }
        
        .sb-sidenav-dark {
            background-color: #212529;
            color: rgba(255, 255, 255, 0.5);
        }
        
        .sb-sidenav-dark .sb-sidenav-menu .nav-link {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .sb-sidenav-dark .sb-sidenav-menu .nav-link:hover {
            color: #fff;
        }
        
        .sb-sidenav-dark .sb-sidenav-menu .nav-link.active {
            color: #fff;
            font-weight: 500;
        }
        
        .sb-sidenav-dark .sb-sidenav-menu .nav-link .sb-nav-link-icon {
            color: rgba(255, 255, 255, 0.25);
        }
        
        #layoutSidenav {
            display: flex;
        }
        
        #layoutSidenav #layoutSidenav_nav {
            flex-basis: 225px;
            flex-shrink: 0;
            transition: transform 0.15s ease-in-out;
            z-index: 1038;
            position: fixed;
            left: 0;
            top: 56px;
            bottom: 0;
            width: 225px;
        }
        
        #layoutSidenav #layoutSidenav_content {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 0;
            flex-grow: 1;
            min-height: calc(100vh - 56px);
            margin-left: 225px;
        }
        
        .sb-sidenav-menu {
            overflow-y: auto;
        }
        
        .sb-nav-fixed #layoutSidenav #layoutSidenav_nav .sb-sidenav .sb-sidenav-menu {
            padding-top: 0;
        }
        
        .sb-nav-fixed .sb-topnav {
            z-index: 1039;
            position: fixed;
            width: 100%;
            top: 0;
        }
        
        .sb-sidenav-footer {
            padding: 0.75rem;
            background-color: #343a40;
            color: rgba(255, 255, 255, 0.5);
        }
        
        @media (max-width: 992px) {
            #layoutSidenav #layoutSidenav_nav {
                transform: translateX(-225px);
            }
            
            #layoutSidenav #layoutSidenav_content {
                margin-left: 0;
            }
            
            .sb-sidenav-toggled #layoutSidenav #layoutSidenav_nav {
                transform: translateX(0);
            }
            
            .sb-sidenav-toggled #layoutSidenav #layoutSidenav_content:before {
                content: "";
                display: block;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: #000;
                z-index: 1037;
                opacity: 0.5;
                transition: opacity 0.3s ease-in-out;
            }
        }
        
        /* Sidebar toggled state for desktop */
        .sb-sidenav-toggled.sb-nav-fixed #layoutSidenav #layoutSidenav_nav {
            transform: translateX(-225px);
        }
        
        .sb-sidenav-toggled.sb-nav-fixed #layoutSidenav #layoutSidenav_content {
            margin-left: 0;
        }
        
        /* Fix main content padding */
        .sb-nav-fixed #layoutSidenav #layoutSidenav_content {
            padding-top: 56px;
        }
        
        /* Menu items styling */
        .sb-sidenav-menu .nav {
            padding-top: 1rem;
        }
        
        .sb-sidenav-menu .nav-link {
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
        }
        
        .sb-sidenav-menu .sb-nav-link-icon {
            margin-right: 0.5rem;
            width: 1.25rem;
            text-align: center;
        }
        
        .sb-sidenav-menu-heading {
            padding: 1rem 1rem 0.5rem;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
            opacity: 0.6;
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="{{ route('admin.dashboard') }}">Sneaker Shop Admin</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Tìm kiếm..." aria-label="Tìm kiếm..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#!">Cài đặt</a></li>
                    <li><a class="dropdown-item" href="#!">Lịch sử hoạt động</a></li>
                    <li><hr class="dropdown-divider" /></li>
                    <li>
                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Đăng xuất
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Tổng quan</div>
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        
                        <div class="sb-sidenav-menu-heading">Quản lý sản phẩm</div>
                        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-list"></i></div>
                            Danh mục
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-tags"></i></div>
                            Thương hiệu
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                            Sản phẩm
                        </a>
                        
                        <div class="sb-sidenav-menu-heading">Quản lý đơn hàng</div>
                        <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                            Đơn hàng
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}" href="{{ route('admin.coupons.index') }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-tag"></i></div>
                            Mã giảm giá
                        </a>
                        
                        <div class="sb-sidenav-menu-heading">Người dùng</div>
                        <a class="nav-link {{ request()->routeIs('admin.users.*') && request('role') === 'customer' ? 'active' : '' }}" 
                           href="{{ route('admin.users.index', ['role' => 'customer']) }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Khách hàng
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.users.*') && request('role') === 'admin' ? 'active' : '' }}" 
                           href="{{ route('admin.users.index', ['role' => 'admin']) }}">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-shield"></i></div>
                            Quản trị viên
                        </a>
                        
                        <div class="sb-sidenav-menu-heading">Báo cáo & Thống kê</div>
                        <a class="nav-link" href="#">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                            Doanh thu
                        </a>
                        <a class="nav-link" href="#">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-pie"></i></div>
                            Sản phẩm bán chạy
                        </a>
                        
                        <div class="sb-sidenav-menu-heading">Hệ thống</div>
                        <a class="nav-link" href="#">
                            <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                            Cài đặt
                        </a>
                        <a class="nav-link" href="{{ route('home') }}" target="_blank">
                            <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                            Xem cửa hàng
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Đăng nhập với:</div>
                    {{ Auth::user()->name ?? 'Guest' }}
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                @yield('content')
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">© {{ date('Y') }} Sneaker Shop</div>
                        <div>
                            <a href="#">Quyền riêng tư</a>
                            &middot;
                            <a href="#">Điều khoản &amp; Điều kiện</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
    <script>
        // Toggle the side navigation
        window.addEventListener('DOMContentLoaded', event => {
            const sidebarToggle = document.body.querySelector('#sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', event => {
                    event.preventDefault();
                    document.body.classList.toggle('sb-sidenav-toggled');
                    localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
                });
            }
            
            // Load previous sidebar state
            const storedSidebarState = localStorage.getItem('sb|sidebar-toggle');
            if (storedSidebarState === 'true') {
                document.body.classList.add('sb-sidenav-toggled');
            }
        });
        
        // Initialize simple-datatables
        document.addEventListener('DOMContentLoaded', function () {
            const datatablesElements = document.querySelectorAll('.datatable-table');
            if (datatablesElements.length > 0) {
                datatablesElements.forEach(element => {
                    new simpleDatatables.DataTable(element);
                });
            }
        });
    </script>
    
    @yield('scripts')
    @stack('scripts')
</body>
</html>