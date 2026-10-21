@extends('layouts.app')
@section('title', 'Roles & Permissions')

@section('content')

@php
    // Core permissions: slug can't be changed and they can't be deleted
    $protected = ['posts.create', 'posts.edit', 'posts.delete', 'admin.access'];
@endphp


<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0"> Roles & Permissions</h4>
        <small class="text-muted">Manage what each role can do</small>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-success btn-sm rounded-pill px-3" type="button"
                data-bs-toggle="collapse" data-bs-target="#addPermissionForm"
                aria-expanded="{{ $errors->any() ? 'true' : 'false' }}" aria-controls="addPermissionForm">
            <i class="bi bi-plus-lg me-1"></i> Add Permission
        </button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>


@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger rounded-3">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<div class="collapse {{ $errors->any() ? 'show' : '' }}" id="addPermissionForm">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4">
            <h6 class="fw-bold mb-0">
                <i class="bi bi-plus-circle me-2 text-success"></i>Add New Permission
            </h6>
        </div>
        <div class="card-body px-4 pb-4">
            <form action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Permission Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control rounded-3"
                               placeholder="e.g. Export reports" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug') }}" class="form-control rounded-3"
                               placeholder="e.g. reports.export" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Group</label>
                        <input type="text" name="group" value="{{ old('group') }}" class="form-control rounded-3"
                               placeholder="e.g. Reports" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-success rounded-pill px-4 flex-grow-1">
                            <i class="bi bi-plus-lg me-1"></i> Add
                        </button>
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3"
                                data-bs-toggle="collapse" data-bs-target="#addPermissionForm">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="row g-3 mb-4">
    @foreach(['admin' => 'danger', 'author' => 'success', 'user' => 'secondary'] as $role => $color)
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-{{ $color }} bg-opacity-10 p-3">
                    <i class="bi bi-shield text-{{ $color }} fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-capitalize">{{ $role }}</h6>
                    <small class="text-muted">
                        {{ $roleCounts[$role] ?? 0 }} {{ Str::plural('user', $roleCounts[$role] ?? 0) }} •
                        {{ count($rolePermissions[$role] ?? []) }} / {{ $permissions->flatten()->count() }} permissions
                    </small>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>


<form action="{{ route('admin.permissions.update') }}" method="POST">
    @csrf
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold mb-0"><i class="bi bi-grid-3x3 me-2 text-primary"></i>Permission Matrix</h6>
                <small class="text-muted">Admin always has all permissions</small>
            </div>
            <button type="submit" class="btn btn-primary rounded-pill px-4 btn-sm">
                <i class="bi bi-save me-1"></i> Save Changes
            </button>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width:40%">Permission</th>
                        <th class="text-center">
                            <span class="badge bg-danger rounded-pill px-3">Admin</span>
                        </th>
                        <th class="text-center">
                            <span class="badge bg-success rounded-pill px-3">Author</span>
                        </th>
                        <th class="text-center">
                            <span class="badge bg-secondary rounded-pill px-3">User</span>
                        </th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $group => $perms)

                    {{-- Group heading --}}
                    <tr class="table-light">
                        <td colspan="5" class="ps-4 fw-bold text-primary small text-uppercase"
                            style="letter-spacing:.05em;font-size:.75rem;">
                            <i class="bi bi-folder2 me-2"></i>{{ $group }}
                        </td>
                    </tr>

                    @foreach($perms as $perm)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-semibold small">{{ $perm->name }}</div>
                            <div class="text-muted" style="font-size:.75rem;">{{ $perm->slug }}</div>
                        </td>

                        
                        <td class="text-center">
                            <div class="form-check d-flex justify-content-center">
                                <input type="checkbox" class="form-check-input"
                                       checked disabled
                                       style="width:18px;height:18px;">
                            </div>
                        </td>

                        
                        <td class="text-center">
                            <div class="form-check d-flex justify-content-center">
                                <input type="checkbox"
                                       class="form-check-input"
                                       name="permissions[author][]"
                                       value="{{ $perm->id }}"
                                       style="width:18px;height:18px;"
                                       @if(in_array((int) $perm->id, array_map('intval', $rolePermissions['author'] ?? []), true)) checked @endif>
                            </div>
                        </td>

                        
                        <td class="text-center">
                            <div class="form-check d-flex justify-content-center">
                                <input type="checkbox"
                                       class="form-check-input"
                                       name="permissions[user][]"
                                       value="{{ $perm->id }}"
                                       style="width:18px;height:18px;"
                                       @if(in_array((int) $perm->id, array_map('intval', $rolePermissions['user'] ?? []), true)) checked @endif>
                            </div>
                        </td>

                        
                        <td class="text-center pe-4">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-3"
                                    title="Edit"
                                    data-url="{{ route('admin.permissions.details', $perm) }}"
                                    data-name="{{ $perm->name }}"
                                    data-slug="{{ $perm->slug }}"
                                    data-group="{{ $perm->group }}"
                                    data-locked="{{ in_array($perm->slug, $protected) ? '1' : '0' }}"
                                    onclick="openEditModal(this)">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            @if(!in_array($perm->slug, $protected))
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                    title="Delete"
                                    data-url="{{ route('admin.permissions.destroy', $perm) }}"
                                    data-name="{{ $perm->name }}"
                                    onclick="openDeleteModal(this)">
                                <i class="bi bi-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</form>


<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="editPermissionForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Permission Name</label>
                        <input type="text" name="name" id="editName" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Slug</label>
                        <input type="text" name="slug" id="editSlug" class="form-control rounded-3" required>
                        <div class="form-text" id="editSlugHelp"></div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Group</label>
                        <input type="text" name="group" id="editGroup" class="form-control rounded-3" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="deletePermissionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-danger text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="bi bi-trash3 text-danger display-3 mb-3 d-block"></i>
                <h5 class="fw-bold">Are you sure?</h5>
                <p class="mb-0">Delete the permission "<strong id="deletePermName"></strong>"? This cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light px-4 rounded-3 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                <form id="deletePermissionForm" method="POST" action="">
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
    
    function openEditModal(btn) {
        document.getElementById('editPermissionForm').action = btn.dataset.url;
        document.getElementById('editName').value  = btn.dataset.name;
        document.getElementById('editSlug').value  = btn.dataset.slug;
        document.getElementById('editGroup').value = btn.dataset.group;

        
        var locked = btn.dataset.locked === '1';
        document.getElementById('editSlug').readOnly = locked;
        document.getElementById('editSlugHelp').textContent = locked
            ? 'This is a core permission, so its slug cannot be changed.'
            : 'Lowercase letters, numbers and dots only, e.g. reports.export';

        new bootstrap.Modal(document.getElementById('editPermissionModal')).show();
    }

    
    function openDeleteModal(btn) {
        document.getElementById('deletePermissionForm').action = btn.dataset.url;
        document.getElementById('deletePermName').textContent = btn.dataset.name;
        new bootstrap.Modal(document.getElementById('deletePermissionModal')).show();
    }
</script>

@endsection