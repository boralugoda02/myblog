@extends('layouts.app')
@section('title', 'Categories')

@section('content')
<div class="container-fluid px-0">

    
    <div class="card border-0 shadow-sm p-4 mb-4 rounded-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1">Category Management</h4>
                <p class="text-muted mb-0 small">Meke category ekathukirima saha thiyena category beliya heka.</p>
            </div>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fs-6">
                Total Categories: {{ $categories->count() }}
            </span>
        </div>
    </div>

    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-3">
                <h5 class="fw-bold mb-3">Create New Category</h5>
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label small text-muted fw-semibold">Category Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Technology, Food, Travel" required>
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        + Save Category
                    </button>
                </form>
            </div>
        </div>

        
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-3">
                <form action="{{ route('categories.index') }}" method="GET" class="p-3 border-bottom">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search category by name...">
                        <button type="submit" class="btn btn-primary">Search</button>
                        @if(request('search'))
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Clear</a>
                        @endif
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase text-muted">
                            <tr>
                                <th>Category Name</th>
                                <th>Slug</th>
                                <th>Total Posts</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $category->name }}</td>
                                    <td class="text-muted small">{{ $category->slug }}</td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                            {{ $category->posts_count ?? $category->posts()->count() }} Posts
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        {{-- Edit / Delete: admin ta witharai --}}
                                        @can('manage-categories')
                                            <button class="btn btn-outline-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $category->id }}">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>

                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm rounded-2 shadow-sm" 
                                                    onclick="confirmCategoryDelete('{{ route('categories.destroy', $category->id) }}')">
                                                <i class="bi bi-trash-fill"></i> Delete
                                            </button>
                                        @endcan
                                    </td>
                                </tr>

                                
                                <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Edit Category</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('categories.update', $category->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body text-start">
                                                    <div class="mb-3">
                                                        <label class="form-label small text-muted fw-semibold">Category Name</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Update Category</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No categories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-danger text-white rounded-top-4">
                <h5 class="modal-title fw-bold" id="deleteCategoryModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Category Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="bi bi-trash3 text-danger display-3 mb-3 d-block"></i>
                <h5 class="fw-bold text-dark">Are you sure?</h5>
                <p class="text-muted mb-0">Are you sure you want to delete this category? This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light px-4 rounded-3 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteCategoryForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4 rounded-3 fw-semibold shadow-sm">
                        Yes, Delete Category
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmCategoryDelete(deleteUrl) {
        document.getElementById('deleteCategoryForm').action = deleteUrl;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
        deleteModal.show();
    }
</script>
@endsection