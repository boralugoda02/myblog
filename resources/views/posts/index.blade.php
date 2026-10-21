@extends('layouts.app')
@section('title', 'All Blogs')
@section('content')
<div class="container-fluid px-0">

    
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold m-0 text-dark">All Blog Posts</h4>
            <small class="text-muted">Manage and view all your published articles</small>
        </div>
        @auth
            <a href="{{ route('posts.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add New Post
            </a>
        @endauth
    </div>

    
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('posts.index') }}" method="GET" class="row g-2">
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

    
    <div class="row g-4">
        @forelse($posts as $post)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden d-flex flex-column justify-content-between">
                    <div>
                        
                        @if($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" class="card-img-top" alt="{{ $post->title }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-image text-secondary display-4"></i>
                            </div>
                        @endif

                        
                        <div class="card-body p-4">
                            <span class="badge bg-primary-subtle text-primary mb-2 px-3 py-2 rounded-pill">
                                {{ $post->category->name ?? 'General' }}
                            </span>
                            <h5 class="card-title fw-bold text-dark mb-2">{{ Str::limit($post->title, 50) }}</h5>
                            <p class="card-text text-muted small">
                                {{ Str::limit(strip_tags($post->content), 90) }}
                            </p>
                        </div>
                    </div>

                    
                    <div class="card-footer bg-white border-top-0 p-4 pt-0 d-flex justify-content-between align-items-center">
                        <small class="text-muted"><i class="bi bi-calendar3 me-1"></i> {{ $post->created_at ? $post->created_at->format('M d, Y') : 'N/A' }}</small>
                        
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('posts.show', $post->slug ?? $post->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                           
                            @can('update', $post)
                                <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endcan

                            @can('delete', $post)
                                <button type="button" 
                                        class="btn btn-outline-danger btn-sm rounded-2" 
                                        title="Delete"
                                        onclick="confirmPostDelete('{{ route('posts.destroy', $post->id) }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="bg-white p-5 text-center rounded-3 shadow-sm border-0">
                    <i class="bi bi-journal-x display-1 text-muted"></i>
                    <h5 class="fw-bold mt-3 text-secondary">No Blog Posts Found</h5>
                    <p class="text-muted mb-4">You haven't added any blog posts yet.</p>
                    @auth
                        <a href="{{ route('posts.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Create First Post
                        </a>
                    @endauth
                </div>
            </div>
        @endforelse
    </div>

    
    @if(method_exists($posts, 'links'))
        <div class="d-flex justify-content-center mt-4">
            {{ $posts->links() }}
        </div>
    @endif

</div>


<div class="modal fade" id="deletePostModal" tabindex="-1" aria-labelledby="deletePostModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-danger text-white rounded-top-4">
                <h5 class="modal-title fw-bold" id="deletePostModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Move to Trash
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    <button type="submit" class="btn btn-danger px-4 rounded-3 fw-semibold shadow-sm">
                        Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmPostDelete(deleteUrl) {
        document.getElementById('deletePostForm').action = deleteUrl;
        var deleteModal = new bootstrap.Modal(document.getElementById('deletePostModal'));
        deleteModal.show();
    }
</script>
@endsection