@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')

    <style>
        .stat-link { text-decoration: none; color: inherit; display: block; }
        .stat-link .card { transition: transform .15s, box-shadow .15s; }
        .stat-link:hover .card { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(15,23,42,.15) !important; }
    </style>

    <div class="rounded-4 p-4 mb-4 text-white d-flex justify-content-between align-items-center flex-wrap gap-3"
         style="background: linear-gradient(90deg, #0f172a, #1e293b);">
        <div>
            <h3 class="fw-bold mb-1">Hi, {{ auth()->user()->name }} 👋</h3>
            <div class="opacity-75">
                @if($pendingCount > 0)
                    {{ $pendingCount }} {{ \Illuminate\Support\Str::plural('post', $pendingCount) }} waiting for your approval.
                @else
                    All are approved.
                @endif
            </div>
        </div>
        <a href="{{ route('admin.posts.status', 'pending') }}" class="btn btn-primary btn-sm rounded-3 fw-semibold px-3">Review queue →</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.posts.status', 'pending') }}" class="stat-link">
                <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white fs-4" style="width:52px;height:52px;background:#f97316;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold lh-1">{{ $pendingCount }}</div>
                        <small class="text-muted">Pending approval</small>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.posts.status', 'published') }}" class="stat-link">
                <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white fs-4" style="width:52px;height:52px;background:#14b8a6;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold lh-1">{{ $publishedCount }}</div>
                        <small class="text-muted">Published</small>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.posts.status', 'rejected') }}" class="stat-link">
                <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white fs-4" style="width:52px;height:52px;background:#ec4899;">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold lh-1">{{ $rejectedCount }}</div>
                        <small class="text-muted">Rejected</small>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.users') }}" class="stat-link">
                <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white fs-4" style="width:52px;height:52px;background:#0d6efd;">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold lh-1">{{ $usersCount }}</div>
                        <small class="text-muted">Users</small>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('categories.index') }}" class="btn btn-light rounded-pill shadow-sm btn-sm px-3">
            <i class="bi bi-folder"></i> Categories <span class="text-primary fw-bold ms-1">{{ $categoriesCount }}</span>
        </a>
        <span class="btn btn-light rounded-pill shadow-sm btn-sm px-3 disabled">
            <i class="bi bi-tags"></i> Tags <span class="text-primary fw-bold ms-1">{{ $tagsCount }}</span>
        </span>
        <a href="{{ route('posts.trash') }}" class="btn btn-light rounded-pill shadow-sm btn-sm px-3">
            <i class="bi bi-trash"></i> Trash <span class="text-primary fw-bold ms-1">{{ $trashCount }}</span>
        </a>
        <span class="btn btn-light rounded-pill shadow-sm btn-sm px-3 disabled">
            <i class="bi bi-chat-dots"></i> Comments <span class="text-primary fw-bold ms-1">{{ $commentsCount }}</span>
        </span>
    </div>

    <div class="card border-0 shadow-sm rounded-4" id="waiting">
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
            <h6 class="fw-bold m-0"><i class="bi bi-hourglass-split"></i> Waiting for approval</h6>
            <a href="{{ route('admin.posts.status', 'pending') }}" class="small text-decoration-none">View all →</a>
        </div>

        @forelse($pendingPosts as $post)
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div>
                    <div class="fw-semibold">{{ \Illuminate\Support\Str::limit($post->title, 60) }}</div>
                    <small class="text-muted">
                        by {{ $post->user->name ?? 'Unknown' }}
                        @if($post->category) • {{ $post->category->name }} @endif
                        • {{ $post->created_at->diffForHumans() }}
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('posts.show', $post) }}" class="btn btn-outline-secondary btn-sm rounded-pill">View</a>

                    <form method="POST" action="{{ route('admin.posts.approve', $post) }}">
                        @csrf
                        <button class="btn btn-success btn-sm rounded-pill px-3">Approve</button>
                    </form>

                    <form method="POST" action="{{ route('admin.posts.reject', $post) }}">
                        @csrf
                        <button class="btn btn-danger btn-sm rounded-pill px-3">Reject</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-5">
                <i class="bi bi-emoji-smile fs-1"></i>
                <div class="mt-2">No posts to approve.</div>
            </div>
        @endforelse
    </div>

@endsection