@extends('layouts.app')
@section('title', 'Activity Log')

@section('content')
@php
    $badge = fn ($a) => match (true) {
        str_ends_with($a, 'approved') => 'success',
        str_ends_with($a, 'rejected'), str_ends_with($a, 'deleted') => 'danger',
        str_starts_with($a, 'auth.')  => 'secondary',
        default                       => 'primary',
    };
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-clock-history me-2"></i>Activity Log</h4>
</div>

<form method="GET" class="card card-body mb-3">
    <div class="row g-2">
        <div class="col-md-3">
            <select name="type" class="form-select" onchange="this.form.submit()">
                <option value="all"  @selected($type === 'all')>All activity</option>
                <option value="post" @selected($type === 'post')>Posts</option>
                <option value="user" @selected($type === 'user')>Users</option>
                <option value="auth" @selected($type === 'auth')>Logins</option>
            </select>
        </div>
        <div class="col-md-7">
            <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Search...">
        </div>
        <div class="col-md-2 d-grid">
            <button class="btn btn-primary">Search</button>
        </div>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>When</th>
                    <th>Who</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="text-nowrap" title="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                        {{ $log->created_at->diffForHumans() }}
                    </td>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                    <td><span class="badge bg-{{ $badge($log->action) }}">{{ $log->action }}</span></td>
                    <td>{{ $log->description }}</td>
                    <td class="text-muted small">{{ $log->ip_address }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No activity yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $logs->links() }}</div>
@endsection