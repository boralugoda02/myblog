@extends('layouts.app')
@section('title', 'Tags')

@section('title', 'Tags - MyBlog')

@section('content')
<div class="container-fluid px-0">
    <h4 class="fw-bold text-dark mb-4"><i class="bi bi-tags me-2"></i>All tags</h4>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4 d-flex flex-wrap gap-2">
            @forelse($tags as $tag)
                <a href="{{ route('tags.show', $tag->slug) }}"
                   class="badge rounded-pill text-bg-light border text-decoration-none fs-6 fw-normal">
                    #{{ $tag->name }} <span class="text-muted">{{ $tag->posts_count }}</span>
                </a>
            @empty
                <span class="text-muted">No tags yet.</span>
            @endforelse
        </div>
    </div>
</div>
@endsection