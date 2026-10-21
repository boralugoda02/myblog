@extends('layouts.app')
@section('title', 'New Role')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">New Role</h4>
        <small class="text-muted">Choose what this role is allowed to do.</small>
    </div>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

@include('admin.roles._form', [
    'action'      => route('admin.roles.store'),
    'method'      => 'POST',
    'role'        => null,
    'readonly'    => false,
    'submitLabel' => 'Create Role',
])
@endsection