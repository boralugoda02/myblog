@php
    $navCategories = \App\Models\Category::orderBy('name')->get();
@endphp

<style>
    :root {
        --navy: #0f172a;
        --navy-light: #1e293b;
        --blue: #0d6efd;
    }
    .site-header { background: var(--navy); }
    .pill-glass { background: var(--navy-light); color: #fff; border: 1px solid #334155; }
    .pill-glass:hover { background: #334155; color: #fff; }
    .search-pill {
        background: var(--navy-light); border: 1px solid #334155;
        color: #fff; min-width: 260px;
    }
    .search-pill::placeholder { color: #94a3b8; }
    .search-pill:focus { background: var(--navy-light); color: #fff; box-shadow: none; border-color: var(--blue); }

    
    .theme-toggle-btn {
        background: rgba(255,255,255,0.1);
        border: 1.5px solid rgba(255,255,255,0.25);
        color: #fff;
        padding: 5px 14px;
        border-radius: 20px;
        cursor: pointer;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: all 0.3s;
    }
    .theme-toggle-btn:hover {
        background: rgba(255,255,255,0.2);
    }
</style>

<nav class="site-header px-4 py-3 mb-4 shadow-sm">
    <div class="container d-flex align-items-center gap-2 flex-wrap">

        <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none me-2">
            <i class="bi bi-feather fs-4" style="color: var(--blue);"></i>
            <span class="fw-bold fs-4 text-white">MyBlog</span>
        </a>

        <a href="{{ route('home') }}" class="btn btn-primary btn-sm rounded-pill px-3">
            <i class="bi bi-house-fill"></i> Home
        </a>

        <div class="dropdown">
            <button class="btn btn-sm rounded-pill px-3 pill-glass" data-bs-toggle="dropdown">
                <i class="bi bi-grid"></i> Categories
            </button>
            <ul class="dropdown-menu shadow border-0">
                @foreach($navCategories as $cat)
                    <li>
                        <a class="dropdown-item" href="{{ route('posts.index', ['category' => $cat->id]) }}">
                            {{ $cat->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <form action="{{ route('posts.index') }}" method="GET" class="flex-grow-1 d-flex justify-content-center">
            <div class="position-relative w-100" style="max-width: 420px;">
                <input type="text" name="search" class="form-control rounded-pill ps-3 pe-5 search-pill"
                       placeholder="Search posts..." value="{{ request('search') }}">
                <button class="btn position-absolute top-50 end-0 translate-middle-y text-white me-1" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>

        @guest
            <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">Login</a>
            <a href="{{ route('register') }}" class="btn btn-primary btn-sm rounded-pill px-3">Register</a>
        @endguest

        @auth
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="btn btn-danger btn-sm rounded-pill px-3">Admin Panel</a>
            @endif
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">My Dashboard</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-light btn-sm rounded-pill px-3">Logout</button>
            </form>
        @endauth

        
        <button class="theme-toggle-btn" onclick="toggleTheme()" id="themeToggle">
            <span id="themeIcon">🌙</span>
            <span id="themeText">Dark</span>
        </button>

    </div>
</nav>

