@extends('layouts.app')
@section('title', $post->title)

@php
    // SEO / WhatsApp preview (Open Graph) values - meta fields empty nam post eke data walin fallback wenawa
    $ogTitle       = $post->meta_title ?: $post->title;
    $ogDescription = $post->meta_description ?: Str::limit(html_entity_decode(strip_tags($post->content)), 160);
    $ogImagePath   = $post->meta_image ?: $post->image;
    $ogImage       = $ogImagePath ? asset('storage/' . $ogImagePath) : asset('images/bg.jpg');

    $shareUrl  = route('posts.show', $post->slug);
    $shareText = $post->title;
@endphp

@section('title', $ogTitle . ' - MyBlog')

@push('meta')
    <meta name="description" content="{{ $ogDescription }}">
    @if($post->meta_keywords)
        <meta name="keywords" content="{{ $post->meta_keywords }}">
    @endif
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="MyBlog">
    <meta property="og:title" content="{{ $ogTitle }} - MyBlog">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:url" content="{{ $shareUrl }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="canonical" href="{{ $shareUrl }}">
    <link rel="alternate" type="application/rss+xml" title="MyBlog RSS" href="{{ route('feed') }}">
    <meta property="article:published_time" content="{{ $post->created_at->toIso8601String() }}">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
@endpush

@section('content')

<style>
    
    .post-body h1, .post-body h2, .post-body h3, .post-body h4 { font-weight: 700; margin: 1.4em 0 .5em; }
    .post-body p { margin-bottom: 1em; }
    .post-body img { max-width: 100%; height: auto; border-radius: 8px; }
    .post-body figure { margin: 1.5em 0; }
    .post-body figure.image { display: table; margin: 1.5em auto; max-width: 100%; }
    .post-body figure.image img { display: block; margin: 0 auto; max-width: 100%; height: auto; }
    .post-body figure.image.image_resized { box-sizing: border-box; display: block; max-width: 100%; }
    .post-body figure.image.image_resized img { width: 100%; }
    .post-body figure.image figcaption { display: table-caption; caption-side: bottom; font-size: .85em; color: #6b7280; text-align: center; padding: .5em; }
    .post-body figure.image-style-side { float: right; margin-left: 1.5em; max-width: 50%; }
    .post-body figure.image-style-align-left { float: left; margin-right: 1.5em; }
    .post-body figure.image-style-align-right { float: right; margin-left: 1.5em; }
    .post-body figure.image-style-block-align-left { margin-left: 0; margin-right: auto; }
    .post-body figure.image-style-block-align-right { margin-right: 0; margin-left: auto; }
    .post-body figure.table { display: block; overflow-x: auto; }
    .post-body table { border-collapse: collapse; width: 100%; }
    .post-body th, .post-body td { border: 1px solid #dee2e6; padding: .5rem .75rem; }
    .post-body th { background: #f8f9fa; }
    .post-body blockquote { border-left: 4px solid #dee2e6; margin: 1.2em 0; padding: .2em 1em; font-style: italic; }
    .post-body pre { background: #0f172a; color: #e2e8f0; padding: 1rem; border-radius: 8px; overflow-x: auto; }
    .post-body .text-tiny { font-size: .7em; }
    .post-body .text-small { font-size: .85em; }
    .post-body .text-big { font-size: 1.4em; }
    .post-body .text-huge { font-size: 1.8em; }
    .post-body::after { content: ""; display: block; clear: both; }

    .mini-thumb { width: 56px; height: 56px; object-fit: cover; border-radius: 8px; flex-shrink: 0; }
</style>

<div class="container-fluid px-0">

    {{-- Flash Messages --}}
    @include('components.flash-messages')

    <div class="mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="row g-4">

        
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                @if($post->image)
                    <img src="{{ asset('storage/' . $post->image) }}" class="w-100" style="max-height: 400px; object-fit: cover;" alt="{{ $post->title }}">
                @endif

                <div class="card-body p-4 p-md-5">
                    <span class="badge bg-primary mb-3">{{ $post->category->name ?? 'Uncategorized' }}</span>
                    <h2 class="fw-bold text-dark mb-3">{{ $post->title }}</h2>

                    {{-- Tags --}}
                    @include('posts.partials.tags', ['post' => $post])

                    {{-- Author + date + reading time + views --}}
                    <div class="d-flex align-items-center p-3 bg-light rounded-3 mb-4">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; font-weight: bold; flex-shrink: 0;">
                            {{ strtoupper(substr($post->user->name ?? 'A', 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="m-0 fw-bold text-dark"><i class="bi bi-person me-1"></i> Created by: {{ $post->user->name ?? 'Unknown Author' }}</h6>
                            <small class="text-muted d-block">
                                <i class="bi bi-calendar3 me-1"></i> Published on: {{ $post->created_at->format('F d, Y \a\t h:i A') }}
                                ({{ $post->created_at->diffForHumans() }})
                            </small>
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i> {{ $post->reading_time }} min read
                                <span class="mx-2">&middot;</span>
                                <i class="bi bi-eye me-1"></i> {{ number_format($post->views) }} {{ \Illuminate\Support\Str::plural('view', $post->views) }}
                            </small>
                        </div>
                    </div>

                    {{-- Post content (sanitised HTML; old plain-text posts still keep their line breaks) --}}
                    <div class="post-body lh-lg text-secondary" style="font-size: 1.1rem;">
                        @if($post->content !== strip_tags($post->content))
                            {!! clean($post->content, 'blog') !!}
                        @else
                            {!! nl2br(e($post->content)) !!}
                        @endif
                    </div>

                    {{-- Share buttons --}}
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-4">
                        <span class="fw-semibold small me-1"><i class="bi bi-share me-1"></i> Share:</span>
                        <a href="https://wa.me/?text={{ urlencode($shareText . ' ' . $shareUrl) }}" target="_blank" rel="noopener" class="btn btn-sm btn-success rounded-pill">
                            <i class="bi bi-whatsapp me-1"></i> WhatsApp
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noopener" class="btn btn-sm btn-primary rounded-pill">
                            <i class="bi bi-facebook me-1"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode($shareText) }}" target="_blank" rel="noopener" class="btn btn-sm btn-dark rounded-pill">
                            <i class="bi bi-twitter-x me-1"></i> X
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-url="{{ $shareUrl }}" onclick="copyPostLink(this)">
                            <i class="bi bi-link-45deg me-1"></i> Copy link
                        </button>
                    </div>

                    <hr class="my-5">

                    {{-- Comments + replies --}}
                    @include('posts.partials.comments', ['post' => $post])
                </div>
            </div>

            {{-- Related posts --}}
            @if($related->isNotEmpty())
                <h5 class="fw-bold mt-5 mb-3">More in {{ $post->category->name ?? 'this category' }}</h5>
                <div class="row g-3">
                    @foreach($related as $item)
                        <div class="col-md-4">
                            <a href="{{ route('posts.show', $item->slug) }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" style="height: 120px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary-subtle" style="height: 120px;"></div>
                                    @endif
                                    <div class="card-body p-3">
                                        <div class="fw-semibold small text-dark">{{ \Illuminate\Support\Str::limit($item->title, 60) }}</div>
                                        <small class="text-muted"><i class="bi bi-clock"></i> {{ $item->reading_time }} min read</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        
        <div class="col-lg-4">
            @if($popular->isNotEmpty())
                <div class="card border-0 shadow-sm rounded-3" style="position: sticky; top: 20px;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-fire text-danger me-1"></i> Popular posts</h6>

                        @foreach($popular as $item)
                            <a href="{{ route('posts.show', $item->slug) }}" class="d-flex align-items-center gap-3 text-decoration-none {{ $loop->last ? '' : 'mb-3' }}">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" class="mini-thumb" alt="">
                                @else
                                    <div class="mini-thumb bg-secondary-subtle"></div>
                                @endif
                                <div>
                                    <div class="fw-semibold small text-dark">{{ \Illuminate\Support\Str::limit($item->title, 55) }}</div>
                                    <small class="text-muted"><i class="bi bi-eye"></i> {{ number_format($item->views) }}</small>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="deleteCommentModal" tabindex="-1" aria-labelledby="deleteCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteCommentModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Delete Comment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this comment? Any replies under it will be deleted too.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteCommentForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openDeleteModal(deleteUrl) {
        document.getElementById('deleteCommentForm').action = deleteUrl;
        var modal = new bootstrap.Modal(document.getElementById('deleteCommentModal'));
        modal.show();
    }

    function copyPostLink(btn) {
        var url = btn.dataset.url;
        var original = btn.innerHTML;

        function done() {
            btn.innerHTML = '<i class="bi bi-check2 me-1"></i> Copied!';
            setTimeout(function () { btn.innerHTML = original; }, 2000);
        }

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url).then(done);
        } else {
            var t = document.createElement('textarea');
            t.value = url;
            document.body.appendChild(t);
            t.select();
            document.execCommand('copy');
            document.body.removeChild(t);
            done();
        }
    }
</script>
@endsection