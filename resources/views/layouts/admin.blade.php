<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - MyBlog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --navy: #0f172a;
            --navy-light: #1e293b;
            --blue: #0d6efd;
        }
        body { background-color: #f1f5f9; color: #0f172a; }

        
        .sidebar {
            width: 260px; height: 100vh; position: fixed; top: 0; left: 0; z-index: 1000;
            background: linear-gradient(180deg, var(--navy), #1e3a8a);
            color: #fff; overflow-y: auto; transition: all .3s;
        }
        .sidebar .section-label {
            font-size: .7rem; letter-spacing: .1em; text-transform: uppercase;
            color: #64748b; margin: 18px 22px 6px;
        }
        .sidebar .nav-link {
            color: #cbd5e1; padding: 10px 16px; border-radius: 10px;
            margin: 2px 12px; display: flex; align-items: center;
        }
        .sidebar .nav-link i { margin-right: 12px; }
        .sidebar .nav-link:hover { background: rgba(255,255,255,.1); color: #fff; }
        .sidebar .nav-link.active { background: #fff; color: var(--navy); font-weight: 600; }
        .soon { font-size: .65rem; background: rgba(255,255,255,.15); padding: 2px 8px; border-radius: 20px; margin-left: auto; }

        .main-content { margin-left: 260px; padding: 25px; transition: all .3s; }
        .topbar { background: #fff; border-radius: 14px; box-shadow: 0 2px 10px rgba(0,0,0,.05); }

        @media (max-width: 991.98px) {
            .sidebar { margin-left: -260px; }
            .sidebar.active { margin-left: 0; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

    
    <div class="sidebar pb-4" id="sidebar">
        <div class="d-flex align-items-center justify-content-between px-4 py-4">
            <h4 class="fw-bold m-0 text-white"><i class="bi bi-feather" style="color: #60a5fa;"></i> MyBlog</h4>
            <button class="btn text-white d-lg-none" id="sidebarClose"><i class="bi bi-x-lg"></i></button>
        </div>

        
        <div class="px-3 mb-2">
            <a href="{{ route('posts.create') }}" class="btn btn-primary w-100 rounded-3 fw-semibold">
                <i class="bi bi-plus-lg"></i> New Post
            </a>
        </div>

        <div class="section-label">Menu</div>
        <ul class="nav flex-column">
            <li><a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="{{ route('posts.index') }}" class="nav-link"><i class="bi bi-journal-text"></i> Posts</a></li>
        </ul>

        <div class="section-label">Manage</div>
        <ul class="nav flex-column">
            <li><a href="{{ route('categories.index') }}" class="nav-link"><i class="bi bi-folder"></i> Categories</a></li>
            <li><a href="#" class="nav-link"><i class="bi bi-tags"></i> Tags <span class="soon">Soon</span></a></li>
            <li><a href="#" class="nav-link"><i class="bi bi-chat-dots"></i> Comments <span class="soon">Soon</span></a></li>
            <li><a href="#" class="nav-link"><i class="bi bi-people"></i> Users <span class="soon">Soon</span></a></li>
            <li><a href="{{ route('admin.smtp.index') }}" class="nav-link {{ request()->routeIs('admin.smtp.*') ? 'active' : '' }}"><i class="bi bi-gear"></i> SMTP Settings</a></li>
            <li><a href="{{ route('posts.trash') }}" class="nav-link"><i class="bi bi-trash"></i> Trash</a></li>
        </ul>

        <div class="section-label">Account</div>
        <ul class="nav flex-column">
            <li><a href="{{ route('profile.edit') }}" class="nav-link"><i class="bi bi-person-circle"></i> My Profile</a></li>
            <li><a href="{{ route('home') }}" class="nav-link"><i class="bi bi-house"></i> Back to Site</a></li>
        </ul>
    </div>

    
    <div class="main-content">

        
        <div class="topbar d-flex justify-content-between align-items-center p-3 mb-4">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-dark d-lg-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
                <h5 class="m-0 fw-bold">@yield('title', 'Dashboard')</h5>
            </div>

            
            <div class="dropdown">
                <button class="btn btn-light rounded-pill d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <span class="fw-semibold">{{ auth()->user()->name }}</span>
                    <i class="bi bi-chevron-down small"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">My Profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('dashboard') }}">My Dashboard</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        
        document.getElementById('sidebarToggle')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('active');
        });
        document.getElementById('sidebarClose')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.remove('active');
        });
    </script>
</body>
</html>