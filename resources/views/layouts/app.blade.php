<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@hasSection('title')@yield('title') | MyBlog @else MyBlog @endif</title>
    @stack('meta')

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%230d6efd'><path d='M15.807.531c-.174-.177-.396-.248-.636-.214-2.327.332-4.212 1.321-5.83 2.815-1.921 1.773-3.16 4.072-3.865 6.522-.192.668-.328 1.353-.41 2.051l-.22.01c-.812.037-1.551.332-2.146.853l-.226.198.814.814.226-.198c.414-.363.931-.57 1.503-.596l.206-.01-.334.334c-.426.426-.66.992-.66 1.593 0 .151.015.302.045.45l.088.423.423-.088c.148-.03.299-.045.45-.045.601 0 1.167-.234 1.593-.66l.334-.334-.01.206c-.026.572-.233 1.089-.596 1.503l-.198.226.814.814.198-.226c.521-.595.816-1.334.853-2.146l.01-.22c.698-.082 1.383-.218 2.051-.41 2.45-.705 4.749-1.944 6.522-3.865 1.494-1.618 2.483-3.503 2.815-5.83.034-.24-.037-.462-.214-.636z'/></svg>">

    <style>
        :root {
            --bg-primary:     #f8f9fa;
            --bg-secondary:   #ffffff;
            --bg-card:        #ffffff;
            --bg-sidebar:     #0f172a;
            --text-primary:   #1e293b;
            --text-secondary: #64748b;
            --border-color:   #e2e8f0;
            --topbar-bg:      #ffffff;
            --navy:           #0f172a;
            --navy-light:     #1e293b;
            --blue:           #0d6efd;
        }

        [data-theme="dark"] {
            --bg-primary:     #0f172a;
            --bg-secondary:   #1e293b;
            --bg-card:        #1e293b;
            --bg-sidebar:     #020617;
            --text-primary:   #f1f5f9;
            --text-secondary: #94a3b8;
            --border-color:   #334155;
            --topbar-bg:      #1e293b;
        }

        html, body {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        body {
            background-color: var(--bg-primary) !important;
            color: var(--text-primary) !important;
        }
        .sidebar {
            width: 260px; height: 100vh;
            position: fixed; top: 0; left: 0;
            background-color: var(--bg-sidebar) !important;
            color: #fff; z-index: 1000; transition: all 0.3s;
            overflow: hidden;
        }
        /* Top part (logo + links) scrolls when the screen is short */
        .sidebar-nav {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
        }
        .sidebar-nav::-webkit-scrollbar { width: 6px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        /* Profile + Logout always stay visible */
        .sidebar-bottom { flex-shrink: 0; }

        .sidebar .nav-link {
            color: #94a3b8; padding: 12px 20px;
            border-radius: 8px; margin: 2px 10px;
            display: flex; align-items: center;
        }
        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: var(--navy-light);
        }
        .main-content {
            margin-left: 260px; padding: 25px; transition: all 0.3s;
        }
        .topbar {
            background-color: var(--topbar-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-primary) !important;
        }
        .card {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
            color: var(--text-primary) !important;
        }
        .table {
            --bs-table-bg:           var(--bg-card);
            --bs-table-color:        var(--text-primary);
            --bs-table-border-color: var(--border-color);
        }
        .form-control, .form-select, input, textarea, select {
            background-color: var(--bg-secondary) !important;
            color:            var(--text-primary)  !important;
            border-color:     var(--border-color)  !important;
        }
        input::placeholder, textarea::placeholder {
            color: var(--text-secondary) !important;
        }

        /* Ticked checkboxes / selected radios were invisible (white tick on a white box) */
        .form-check-input:checked {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }

        .dropdown-menu {
            background-color: var(--bg-card)     !important;
            border-color:     var(--border-color) !important;
        }
        .dropdown-item { color: var(--text-primary) !important; }
        .dropdown-item:hover { background-color: var(--bg-secondary) !important; }

        @media (max-width: 991.98px) {
            .sidebar { margin-left: -260px; }
            .sidebar.active { margin-left: 0; }
            .main-content { margin-left: 0; }
        }
    </style>

    @include('layouts._theme')
</head>
<body>


    <div id="page-loader" style="position:fixed;width:100%;height:100%;top:0;left:0;
         background:rgba(15,23,42,0.85);z-index:9999;display:flex;
         justify-content:center;align-items:center;">
        <div class="spinner-border text-primary" style="width:3rem;height:3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>


    <div class="sidebar d-flex flex-column justify-content-between p-3" id="sidebar">
        <div class="sidebar-nav">
            <div class="d-flex align-items-center justify-content-between px-3 mb-4">
                <h4 class="fw-bold m-0 text-white">
                    <i class="bi bi-feather text-primary"></i> MyBlog
                </h4>
                <button class="btn text-white d-lg-none" id="sidebarClose">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-3"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('posts.index') }}"
                       class="nav-link {{ request()->routeIs('posts.index') ? 'active' : '' }}">
                        <i class="bi bi-journal-text me-3"></i> Blogs
                    </a>
                </li>

                @auth
                <li class="nav-item">
                    <a href="{{ route('posts.create') }}"
                       class="nav-link {{ request()->routeIs('posts.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle me-3"></i> Add Post
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('categories.index') }}"
                       class="nav-link {{ request()->routeIs('categories.index') ? 'active' : '' }}">
                        <i class="bi bi-tags me-3"></i> Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('posts.trash') }}"
                       class="nav-link {{ request()->routeIs('posts.trash') ? 'active' : '' }}">
                        <i class="bi bi-trash me-3"></i> Trash
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{ route('profile.edit') }}"
                       class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <i class="bi bi-person-circle me-3"></i> My Profile
                    </a>
                </li>

                @if(auth()->user()->role === 'admin')
                <li class="nav-item mt-3">
                    <small class="text-secondary px-3 text-uppercase"
                           style="font-size:.7rem;letter-spacing:.1em;">Admin</small>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                       class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-shield-check me-3"></i> Admin Dashboard
                    </a>
                </li>
                {{-- Activity Log --}}
                <li class="nav-item">
                    <a href="{{ route('admin.activity.index') }}"
                       class="nav-link {{ request()->routeIs('admin.activity.*') ? 'active' : '' }}">
                        <i class="bi bi-clock-history me-3"></i> Activity Log
                    </a>
                </li>
                {{-- Roles --}}
                <li class="nav-item">
                    <a href="{{ route('admin.roles.index') }}"
                       class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <i class="bi bi-people me-3"></i> Roles
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.permissions.index') }}"
                       class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-lock me-3"></i> Permissions
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.mail.edit') }}"
                       class="nav-link {{ request()->routeIs('admin.mail.*') ? 'active' : '' }}">
                        <i class="bi bi-gear me-3"></i> SMTP Settings
                    </a>
                </li>
                @endif
                @endauth
            </ul>
        </div>

        {{-- Sidebar Bottom --}}
        <div class="pt-3 border-top border-secondary sidebar-bottom">
            @auth
                @php
                    // Avatar can be an uploaded file (path) or a Google picture (full URL)
                    $sidebarAvatar = auth()->user()->avatar;
                    $sidebarAvatarUrl = $sidebarAvatar
                        ? (\Illuminate\Support\Str::startsWith($sidebarAvatar, ['http://', 'https://'])
                            ? $sidebarAvatar
                            : asset('storage/' . $sidebarAvatar))
                        : null;
                @endphp


                <a href="{{ route('profile.edit') }}"
                   class="d-flex align-items-center mb-3 px-2 text-decoration-none"
                   style="color:inherit;">
                    @if($sidebarAvatarUrl)
                        <img src="{{ $sidebarAvatarUrl }}"
                             class="rounded-circle me-3"
                             width="40" height="40"
                             referrerpolicy="no-referrer"
                             style="object-fit:cover;">
                    @else
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center
                                    justify-content-center me-3 fw-bold"
                             style="width:40px;height:40px;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h6 class="m-0 text-white">{{ auth()->user()->name }}</h6>
                        <small class="text-secondary">
                            {{ auth()->user()->role === 'admin' ? 'Admin' : 'User' }}
                        </small>
                    </div>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light w-100 btn-sm">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
            @else
                <div class="d-grid gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-person-plus me-1"></i> Register
                    </a>
                </div>
            @endauth
        </div>
    </div>


    <div class="main-content">


        <div class="topbar d-flex justify-content-between align-items-center mb-4 p-3 rounded shadow-sm">
            <button class="btn btn-dark d-lg-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>

            <div class="d-none d-sm-flex align-items-center gap-3">
                <h5 class="m-0 fw-bold" style="color:var(--text-primary);">Blog Management</h5>
            </div>

            <div class="d-flex align-items-center gap-3">


                @include('layouts._bell')


                <button type="button" class="btn btn-outline-secondary btn-sm"
                        onclick="toggleTheme()" aria-label="Switch theme">
                    <i id="themeIcon" class="bi bi-moon-stars-fill"></i>
                </button>


                @auth
                    <a href="{{ route('profile.edit') }}"
                       class="badge bg-primary-subtle text-primary p-2 text-decoration-none">
                        <i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name }}
                    </a>

                    {{-- Quick logout (always visible) --}}
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Logout">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                @endauth
            </div>
        </div>

        @yield('content')
    </div>

    {{-- Toasts --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        @if(session('success'))
            <div class="toast align-items-center text-bg-success border-0 show" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="toast align-items-center text-bg-danger border-0 show" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('active');
        });
        document.getElementById('sidebarClose')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.remove('active');
        });

        const toastElList = document.querySelectorAll('.toast');
        [...toastElList].map(el => new bootstrap.Toast(el, { delay: 4000 })).forEach(t => t.show());

        window.addEventListener('load', () => {
            const loader = document.getElementById('page-loader');
            if (loader) loader.style.display = 'none';
        });

        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (link && link.href && !link.href.startsWith('#') &&
                !link.href.startsWith('javascript:')) {
                const loader = document.getElementById('page-loader');
                if (loader) loader.style.display = 'flex';
            }
        });

        window.addEventListener('pageshow', (e) => {
            if (e.persisted) {
                const loader = document.getElementById('page-loader');
                if (loader) loader.style.display = 'none';
            }
        });
    </script>
    @stack('scripts')
    @yield('scripts')
</body>
</html>