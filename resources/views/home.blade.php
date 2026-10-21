@section('title', 'Home')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyBlog - Home</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%230d6efd'><path d='M15.807.531c-.174-.177-.396-.248-.636-.214-2.327.332-4.212 1.321-5.83 2.815-1.921 1.773-3.16 4.072-3.865 6.522-.192.668-.328 1.353-.41 2.051l-.22.01c-.812.037-1.551.332-2.146.853l-.226.198.814.814.226-.198c.414-.363.931-.57 1.503-.596l.206-.01-.334.334c-.426.426-.66.992-.66 1.593 0 .151.015.302.045.45l.088.423.423-.088c.148-.03.299-.045.45-.045.601 0 1.167-.234 1.593-.66l.334-.334-.01.206c-.026.572-.233 1.089-.596 1.503l-.198.226.814.814.198-.226c.521-.595.816-1.334.853-2.146l.01-.22c.698-.082 1.383-.218 2.051-.41 2.45-.705 4.749-1.944 6.522-3.865 1.494-1.618 2.483-3.503 2.815-5.83.034-.24-.037-.462-.214-.636z'/></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; color: #0f172a; }

        .card-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,.05);
        }

        
        .hero {
            position: relative; border-radius: 20px; overflow: hidden; min-height: 380px;
            background: var(--navy-light) center/cover no-repeat;
            display: flex; align-items: flex-end;
        }
        .hero::before {
            content: ""; position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(15,23,42,.9), rgba(15,23,42,.1));
        }
        .hero-content { position: relative; padding: 1.8rem; color: #fff; width: 100%; }

        .section-title { border-left: 5px solid var(--blue); padding-left: 12px; font-weight: 700; }
        .rank { color: var(--blue); font-weight: 700; font-size: 1.2rem; }
        .placeholder-img { background: var(--navy-light); }

        
        .site-footer { background: var(--navy); color: #fff; }
        .site-footer a { color: #94a3b8; text-decoration: none; }
        .site-footer a:hover { color: #fff; text-decoration: underline; }
    </style>
    @include('layouts._theme')
</head>
<body>

    @include('layouts.navbar')

    <div class="container pb-5">
        <div class="row g-4">

            
            <div class="col-lg-8">

                
                @if($featuredPost)
                <div class="hero mb-4"
                     @if($featuredPost->image) style="background-image: url('{{ asset('storage/' . $featuredPost->image) }}');" @endif>
                    <div class="hero-content">
                        <span class="badge rounded-pill bg-primary mb-2">
                            <i class="bi bi-stars"></i> Latest
                        </span>
                        <h2 class="fw-bold">{{ $featuredPost->title }}</h2>
                        <p class="mb-3 opacity-75">{{ \Illuminate\Support\Str::limit(strip_tags($featuredPost->content), 110) }}</p>
                        <div class="d-flex align-items-center flex-wrap gap-3 small">
                            <span><i class="bi bi-person-circle"></i> {{ $featuredPost->user->name ?? 'Unknown' }}</span>
                            <span><i class="bi bi-calendar3"></i> {{ $featuredPost->created_at->format('M d, Y') }}</span>
                            <a href="{{ route('posts.show', $featuredPost) }}" class="btn btn-light btn-sm rounded-pill px-3 fw-bold ms-auto">Read more →</a>
                        </div>
                    </div>
                </div>
                @endif

                
                <h4 class="section-title mb-3">Latest Posts</h4>
                <div class="row g-3">
                    @foreach($latestPosts as $post)
                    <div class="col-md-6">
                        <a href="{{ route('posts.show', $post) }}" class="text-decoration-none">
                            <div class="card card-custom overflow-hidden h-100">
                                @if($post->image)
                                    <img src="{{ asset('storage/' . $post->image) }}" class="w-100" style="height: 170px; object-fit: cover;" alt="Post">
                                @else
                                    <div class="placeholder-img" style="height:170px;"></div>
                                @endif
                                <div class="p-3">
                                    <h6 class="fw-bold text-dark mb-1">{{ \Illuminate\Support\Str::limit($post->title, 45) }}</h6>
                                    <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4">{{ $latestPosts->links('pagination::bootstrap-5') }}</div>
            </div>

            
            <div class="col-lg-4">

                
                <div class="card card-custom p-3 mb-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-fire text-danger"></i> Popular Posts</h6>
                    @foreach($popularPosts as $index => $pPost)
                    <a href="{{ route('posts.show', $pPost) }}"
                       class="d-flex align-items-center gap-3 text-decoration-none py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <span class="rank">{{ $index + 1 }}</span>
                        @if($pPost->image)
                            <img src="{{ asset('storage/' . $pPost->image) }}" class="rounded" width="50" height="50" style="object-fit: cover;" alt="Popular">
                        @else
                            <div class="rounded placeholder-img" style="width:50px;height:50px;"></div>
                        @endif
                        <div>
                            <div class="text-dark small fw-semibold">{{ \Illuminate\Support\Str::limit($pPost->title, 28) }}</div>
                            <small class="text-muted"><i class="bi bi-eye"></i> {{ $pPost->views }}</small>
                        </div>
                    </a>
                    @endforeach
                </div>

                
                <div class="card card-custom p-3">
                    <h6 class="fw-bold mb-3"><i class="bi bi-tags-fill text-warning"></i> Categories</h6>
                    @foreach($categories as $category)
                    <a href="{{ route('posts.index', ['category' => $category->id]) }}"
                       class="d-flex justify-content-between align-items-center text-decoration-none text-dark py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <span>{{ $category->name }}</span>
                        <span class="badge bg-primary rounded-pill">{{ $category->posts_count }}</span>
                    </a>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    
    <footer class="site-footer mt-5 pt-5 pb-3">
        <div class="container">
            <div class="row g-4">

                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-feather fs-4" style="color: var(--blue);"></i>
                        <span class="fw-bold fs-4">MyBlog</span>
                    </div>
                    <p class="small" style="color:#94a3b8;">
                        A place to read and share stories about technology, travel, food and everyday life.
                    </p>
                </div>

                <div class="col-md-4">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2"><a href="{{ route('home') }}">Home</a></li>
                        <li class="mb-2"><a href="{{ route('posts.index') }}">All Posts</a></li>
                        @guest
                            <li class="mb-2"><a href="{{ route('login') }}">Login</a></li>
                            <li class="mb-2"><a href="{{ route('register') }}">Register</a></li>
                        @endguest
                        @auth
                            <li class="mb-2"><a href="{{ route('dashboard') }}">My Dashboard</a></li>
                        @endauth
                    </ul>
                </div>

                <div class="col-md-4">
                    <h6 class="fw-bold mb-3">Categories</h6>
                    <ul class="list-unstyled small mb-0">
                        @foreach($categories->take(5) as $cat)
                            <li class="mb-2">
                                <a href="{{ route('posts.index', ['category' => $cat->id]) }}">{{ $cat->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>

            <hr class="opacity-25 mt-4" style="border-color:#334155;">
            <p class="text-center small mb-0" style="color:#94a3b8;">
                &copy; {{ date('Y') }} MyBlog. All rights reserved.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>