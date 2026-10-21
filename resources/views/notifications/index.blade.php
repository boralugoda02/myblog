@extends('layouts.app')
@section('title', 'Notifications')
@section('content')
<div class="container-fluid px-0" style="max-width: 760px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0 text-dark"><i class="bi bi-bell me-2"></i>Notifications</h4>

        @if(auth()->user()->unreadNotifications()->count())
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button class="btn btn-outline-secondary btn-sm">Mark all as read</button>
            </form>
        @endif
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="list-group list-group-flush">
            @forelse($notifications as $n)
                @php
                    $d = $n->data;
                    $icon = match($d['status'] ?? '') {
                        'published' => 'bi-check-circle-fill text-success',
                        'rejected'  => 'bi-x-circle-fill text-danger',
                        'reply'     => 'bi-chat-dots-fill text-primary',
                        
                        default     => 'bi-hourglass-split text-warning',
                    };
                @endphp
                <a href="{{ route('notifications.open', $n->id) }}"
                   class="list-group-item list-group-item-action d-flex gap-3 py-3 {{ $n->read_at ? '' : 'bg-primary-subtle' }}">
                    <i class="bi {{ $icon }} fs-4"></i>
                    <div class="flex-grow-1">
                        <div class="{{ $n->read_at ? '' : 'fw-semibold' }}">{{ $d['message'] ?? 'Notification' }}</div>
                        @if(!empty($d['reason']))
                            <div class="small text-muted mt-1"><strong>Reason:</strong> {{ $d['reason'] }}</div>
                        @endif
                        <div class="small text-muted mt-1">{{ $n->created_at->diffForHumans() }}</div>
                    </div>
                </a>
            @empty
                <div class="p-4 text-center text-muted">No notifications yet.</div>
            @endforelse
        </div>
    </div>

    <div class="mt-3">{{ $notifications->links('pagination::bootstrap-5') }}</div>
</div>
@endsection