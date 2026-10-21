@extends('layouts.app')
@section('title', 'Manage Users')

@section('content')

@php
    // NEW: colours + icons for the role badges (custom roles get blue)
    $roleColors = ['admin' => 'danger', 'author' => 'success', 'user' => 'secondary'];
    $roleIcons  = ['admin' => 'bi-shield-check', 'author' => 'bi-pencil', 'user' => 'bi-person'];

    // NEW: real counts for ALL users (not just this page)
    $roleCounts = \App\Models\User::selectRaw('role, COUNT(*) as total')->groupBy('role')->pluck('total', 'role');
@endphp

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">👥 User Management</h4>
        <small class="text-muted">{{ $users->total() }} registered {{ \Illuminate\Support\Str::plural('user', $users->total()) }}</small>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
</div>


@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif


<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 text-center p-3">
            <div class="fw-bold fs-4 text-primary">{{ $users->total() }}</div>
            <small class="text-muted">Total Users</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 text-center p-3">
            <div class="fw-bold fs-4 text-danger">{{ $roleCounts['admin'] ?? 0 }}</div>
            <small class="text-muted">Admins</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 text-center p-3">
            <div class="fw-bold fs-4 text-secondary">{{ $roleCounts['user'] ?? 0 }}</div>
            <small class="text-muted">Users</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-3 text-center p-3">
            <div class="fw-bold fs-4 text-success">{{ $roleCounts['author'] ?? 0 }}</div>
            <small class="text-muted">Authors</small>
        </div>
    </div>
</div>


<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">#</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $user)
                @php
                    // NEW: avatar can be an uploaded file (path) or a Google picture (full URL)
                    $avatarUrl = $user->avatar
                        ? (\Illuminate\Support\Str::startsWith($user->avatar, ['http://', 'https://'])
                            ? $user->avatar
                            : asset('storage/' . $user->avatar))
                        : null;

                    // NEW: role name, colour and icon for this user
                    $userRole  = $roles->firstWhere('slug', $user->role);
                    $roleName  = $userRole->name ?? ucfirst($user->role);
                    $roleColor = $roleColors[$user->role] ?? 'primary';
                    $roleIcon  = $roleIcons[$user->role] ?? 'bi-person-badge';
                @endphp
                <tr>
                    <td class="ps-3 text-muted small">{{ $users->firstItem() + $index }}</td>


                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($avatarUrl)
                                <img src="{{ $avatarUrl }}" class="rounded-circle"
                                     width="38" height="38" style="object-fit:cover;"
                                     referrerpolicy="no-referrer">
                            @else
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                     style="width:38px;height:38px;font-size:15px;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $user->name }}</div>
                                @if($user->google_id)
                                    <small class="text-muted"><i class="bi bi-google"></i> Google</small>
                                @endif
                            </div>
                        </div>
                    </td>


                    <td class="text-muted small">{{ $user->email }}</td>


                    {{-- CHANGED: role badge (name + colour from the roles table) --}}
                    <td>
                        <span class="badge bg-{{ $roleColor }} rounded-pill px-3">
                            <i class="bi {{ $roleIcon }} me-1"></i>{{ $roleName }}
                        </span>
                    </td>


                    <td class="text-muted small">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ $user->created_at->format('M d, Y') }}
                    </td>


                    <td class="text-center">
                        @if($user->id !== auth()->id())
                            <div class="d-flex gap-2 justify-content-center">

                                {{-- CHANGED: role dropdown lists every role from the roles table --}}
                                <form action="{{ route('admin.users.role', $user) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role"
                                            class="form-select form-select-sm rounded-pill"
                                            style="min-width:120px;"
                                            onchange="this.form.submit()">
                                        @foreach($roles as $r)
                                            <option value="{{ $r->slug }}" {{ $user->role === $r->slug ? 'selected' : '' }}>
                                                {{ $r->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>


                                <button type="button"
                                        class="btn btn-outline-danger btn-sm rounded-pill px-3"
                                        onclick="confirmDelete(
                                            '{{ route('admin.users.delete', $user) }}',
                                            '{{ addslashes($user->name) }}'
                                        )">
                                    <i class="bi bi-trash"></i>
                                </button>

                            </div>
                        @else
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3">
                                <i class="bi bi-person-check me-1"></i> You
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


<div class="mt-3">{{ $users->links('pagination::bootstrap-5') }}</div>


<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-body text-center px-4 pt-4 pb-2">
                {{-- Icon --}}
                <div style="width:72px;height:72px;background:#fee2e2;border-radius:50%;
                            display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="bi bi-trash3-fill text-danger" style="font-size:32px;"></i>
                </div>

                <h5 class="fw-bold mb-2">Delete User?</h5>
                <p class="text-muted mb-1">
                    Are you sure you want to delete
                </p>
                <p class="fw-bold text-danger fs-5 mb-3" id="deleteUserName"></p>
                <p class="text-muted small">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    This action <strong>cannot be undone!</strong>
                </p>
            </div>

            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button type="button"
                        class="btn btn-outline-secondary rounded-pill px-4"
                        data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i> Cancel
                </button>

                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4">
                        <i class="bi bi-trash me-1"></i> Yes, Delete
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function confirmDelete(url, name) {
        document.getElementById('deleteForm').action = url;
        document.getElementById('deleteUserName').textContent = name;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>
@endpush