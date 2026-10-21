@extends('layouts.app')
@section('title', $tag->name . ' - Posts')

@section('title', '#' . $tag->name . ' - MyBlog')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold m-0 text-dark">#{{ $tag->name }}</h4>
            <small class="text-muted">{{ $posts->total() }} {{ \Illuminate\Support\Str::plural('post', $posts->total()) }}</small>
        </div>
        <a href="{{ route('tags.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-tags me-1"></i> All tags
        </a>
    </div>

    <div class="row g-4">
        @forelse($posts as $post)
            <div class="col-md-6 col-xl-4">
                <a href="{{ route('posts.show', $post->slug) }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                        @if($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" style="height: 180px; object-fit: cover;">
                        @else
                            <div class="bg-secondary-subtle" style="height: 180px;"></div>
                        @endif
                        <div class="card-body">
                            <span class="badge bg-primary mb-2">{{ $post->category->name ?? 'Uncategorized' }}</span>
                            <h6 class="fw-bold text-dark">{{ \Illuminate\Support\Str::limit($post->title, 70) }}</h6>
                            <p class="small text-muted mb-2">{{ \Illuminate\Support\Str::limit(html_entity_decode(strip_tags($post->content)), 100) }}</p>
                            <small class="text-muted">
                                {{ $post->user->name ?? 'Unknown' }} &middot; {{ $post->created_at->format('M d, Y') }} &middot; {{ $post->reading_time }} min read
                            </small>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">No posts with this tag yet.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $posts->links('pagination::bootstrap-5') }}</div>
</div>
@endsection