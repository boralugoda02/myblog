@extends('layouts.app')
@section('title', 'Roles & Permissions')

@section('content')
@php
    $colors = ['admin' => 'danger', 'author' => 'success', 'user' => 'secondary'];
@endphp

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0"> Roles & Permissions</h4>
        <small class="text-muted">Control what each role is allowed to do.</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-key me-1"></i> Manage permissions
        </a>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
            <i class="bi bi-plus-lg me-1"></i> New role
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Role</th>
                    <th>Users</th>
                    <th>Permissions</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                @php $color = $colors[$role->slug] ?? 'primary'; @endphp
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-{{ $color }} bg-opacity-10 p-2">
                                <i class="bi bi-shield-check text-{{ $color }} fs-5"></i>
                            </div>
                            <div>
                                <span class="fw-semibold">{{ $role->name }}</span>
                                @if($role->is_system)
                                    <span class="badge bg-secondary-subtle text-secondary ms-1">System</span>
                                @endif
                                <div class="text-muted small">{{ $role->description }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $userCounts[$role->slug] ?? 0 }}</td>
                    <td>
                        @if($role->isAdmin())
                            <span class="badge bg-danger-subtle text-danger">All</span>
                        @else
                            {{ $role->permissions_count }} / {{ $totalPermissions }}
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('admin.roles.edit', $role) }}"
                           class="btn btn-sm btn-outline-primary rounded-3"
                           title="{{ $role->isAdmin() ? 'View' : 'Edit' }}">
                            <i class="bi {{ $role->isAdmin() ? 'bi-eye' : 'bi-pencil-square' }}"></i>
                        </a>

                        @unless($role->is_system)
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3" title="Delete"
                                data-url="{{ route('admin.roles.destroy', $role) }}"
                                data-name="{{ $role->name }}"
                                onclick="openDeleteModal(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                        @endunless
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Delete Role Modal --}}
<div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-danger text-white rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="bi bi-trash3 text-danger display-3 mb-3 d-block"></i>
                <h5 class="fw-bold">Are you sure?</h5>
                <p class="mb-0">Delete the role "<strong id="deleteRoleName"></strong>"? This cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light px-4 rounded-3 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteRoleForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4 rounded-3 fw-semibold shadow-sm">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openDeleteModal(btn) {
        document.getElementById('deleteRoleForm').action = btn.dataset.url;
        document.getElementById('deleteRoleName').textContent = btn.dataset.name;
        new bootstrap.Modal(document.getElementById('deleteRoleModal')).show();
    }
</script>
@endsection