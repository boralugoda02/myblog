@extends('layouts.app')
@section('title', 'Trash')

@section('content')
<div class="container-fluid px-0">

    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    
    <div class="bg-white p-4 rounded shadow-sm border mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold text-dark m-0">Trash (Deleted Posts)</h4>
            <p class="text-muted small m-0 mt-1">You can restore deleted blog posts or permanently delete them from here.</p>
        </div>
        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-semibold">
            Total Deleted: {{ $trashedPosts->count() }}
        </span>
    </div>

    
    <div class="bg-white rounded shadow-sm border overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase small text-muted">
                    <tr>
                        <th class="ps-4">Title</th>
                        <th>Category</th>
                        <th>Deleted At</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trashedPosts as $post)
                        <tr>
                            <td class="ps-4 fw-semibold text-dark">
                                {{ $post->title }}
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    {{ $post->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                {{ $post->deleted_at->format('M d, Y') }}
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    
                                    {{-- Restore Button --}}
                                    <form action="{{ route('posts.restore', $post->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success fw-semibold">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                                        </button>
                                    </form>

                                    {{-- Permanent Delete Button (opens modal) --}}
                                    <button type="button"
                                            class="btn btn-danger btn-sm rounded-2 shadow-sm"
                                            onclick="confirmDelete('{{ route('posts.force-delete', $post->id) }}')">
                                        <i class="bi bi-trash-fill"></i> Delete Forever
                                    </button>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                No deleted posts found in Trash.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>


<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-danger text-white rounded-top-4">
                <h5 class="modal-title fw-bold" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="bi bi-trash3 text-danger display-3 mb-3 d-block"></i>
                <h5 class="fw-bold text-dark">Are you sure?</h5>
                <p class="text-muted mb-0">Are you sure you want to delete this post permanently? This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light px-4 rounded-3 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4 rounded-3 fw-semibold shadow-sm">
                        Yes, Delete Forever
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(deleteUrl) {
        
        document.getElementById('deleteForm').action = deleteUrl;

        
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>
@endsection