@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-0">

    {{-- Flash Messages --}}
    @include('components.flash-messages')

    
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h4 class="fw-bold m-0 text-dark">Dashboard Overview</h4>
        @auth
            <a href="{{ route('posts.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Create New Post
            </a>
        @endauth
    </div>

    
    <div class="row g-3 mb-4">
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small fw-medium mb-1">Total Posts</p>
                        <h3 class="fw-bold text-dark m-0">{{ $posts->count() }}</h3>
                    </div>
                    <div class="p-3 bg-primary-subtle text-primary rounded-3 fs-4">
                        <i class="bi bi-journal-text"></i>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small fw-medium mb-1">Active Categories</p>
                        <h3 class="fw-bold text-dark m-0">{{ $categoriesCount ?? 0 }}</h3>
                    </div>
                    <div class="p-3 bg-purple-subtle text-purple rounded-3 fs-4" style="background-color: #f3e8ff; color: #7e22ce;">
                        <i class="bi bi-tags"></i>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small fw-medium mb-1">Quick Action</p>
                        <a href="{{ route('posts.create') }}" class="fw-semibold text-primary text-decoration-none small">+ Add New Post</a>
                    </div>
                    <div class="p-3 bg-success-subtle text-success rounded-3 fs-4">
                        <i class="bi bi-plus-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('dashboard') }}" method="GET" class="row g-2">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Search blogs by title or content...">
                    </div>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
    </div>

    
    <h5 class="fw-bold text-dark mb-3">{{ auth()->user()->isAdmin() ? 'All Blogs' : 'My Blogs' }}</h5>
    <div class="row g-4">
        @forelse($posts as $post)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden d-flex flex-column justify-content-between">
                    <div>
                        @if($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" class="card-img-top" alt="{{ $post->title }}" style="height: 180px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                <i class="bi bi-file-post text-secondary display-4"></i>
                            </div>
                        @endif

                        <div class="card-body p-3">
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                <span class="badge bg-primary-subtle text-primary d-inline-flex align-items-center">
                                    {{ $post->category->name ?? 'General' }}
                                </span>
                                <x-status-badge :post="$post" />
                            </div>
                            <h6 class="card-title fw-bold text-dark mb-2">{{ Str::limit($post->title, 50) }}</h6>
                            <p class="card-text text-muted small">
                                {{ Str::limit(strip_tags($post->content), 80) }}
                            </p>
                        </div>
                    </div>

                    
                    <div class="card-footer bg-white border-top-0 p-3 pt-0 d-flex justify-content-between align-items-center">
                        <small class="text-muted"><i class="bi bi-clock me-1"></i> {{ $post->created_at ? $post->created_at->format('M d, Y') : 'N/A' }}</small>
                        
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('posts.show', $post->slug ?? $post->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye"></i>
                            </a>
                            {{-- Post eka liyapu Author ta witharak Edit/Delete penenawa (PostPolicy) --}}
                            @can('update', $post)
                                <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-outline-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endcan

                            {{-- Draft / Rejected post ekak approval ekata submit karanna --}}
                            @if(in_array($post->status, ['draft', 'rejected']))
                                @can('update', $post)
                                    <form action="{{ route('posts.submit', $post->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-sm" title="{{ $post->status === 'rejected' ? 'Resubmit' : 'Submit for approval' }}">
                                            <i class="bi bi-send"></i>
                                        </button>
                                    </form>
                                @endcan
                            @endif

                            @can('delete', $post)
                                <button type="button"
                                        class="btn btn-outline-danger btn-sm rounded-2"
                                        onclick="confirmPostDelete('{{ route('posts.destroy', $post->id) }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 bg-white rounded-3 shadow-sm border-0">
                <i class="bi bi-journal-x display-2 text-muted"></i>
                <p class="text-muted mt-3 mb-2">No blogs found.</p>
                <a href="{{ route('posts.create') }}" class="btn btn-primary btn-sm">Add New Post</a>
            </div>
        @endforelse
    </div>
</div>


<div class="modal fade" id="deletePostModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-danger text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Move to Trash
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="bi bi-trash3 text-danger display-3 mb-3 d-block"></i>
                <h5 class="fw-bold text-dark">Are you sure?</h5>
                <p class="text-muted mb-0">Are you sure you want to delete this blog post? It will be moved to Trash.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light px-4 rounded-3 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                <form id="deletePostForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4 rounded-3 fw-semibold shadow-sm">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmPostDelete(deleteUrl) {
        document.getElementById('deletePostForm').action = deleteUrl;
        var modal = new bootstrap.Modal(document.getElementById('deletePostModal'));
        modal.show();
    }
</script>
@endsection