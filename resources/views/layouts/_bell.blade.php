@auth
@php
    $btnClass = $btnClass ?? 'btn-outline-secondary';
    $unreadCount = auth()->user()->unreadNotifications()->count();
    $recentNotifications = auth()->user()->notifications()->latest()->limit(5)->get();
@endphp

<div class="dropdown">
    <button type="button" class="btn {{ $btnClass }} btn-sm position-relative" data-bs-toggle="dropdown" aria-label="Notifications">
        <i class="bi bi-bell{{ $unreadCount ? '-fill' : '' }}"></i>
        @if($unreadCount)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <ul class="dropdown-menu dropdown-menu-end p-0 shadow border-0" style="width: 320px; max-height: 420px; overflow-y: auto;">
        <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
            <strong class="small">Notifications</strong>
            @if($unreadCount)
                <form method="POST" action="{{ route('notifications.readAll') }}">
                    @csrf
                    <button class="btn btn-link btn-sm p-0 text-decoration-none">Mark all read</button>
                </form>
            @endif
        </li>

        @forelse($recentNotifications as $n)
            @php
                $icon = match($n->data['status'] ?? '') {
                    'published' => 'bi-check-circle-fill text-success',
                    'rejected'  => 'bi-x-circle-fill text-danger',
                    'reply'     => 'bi-chat-dots-fill text-primary',
                    
                    default     => 'bi-hourglass-split text-warning',
                };
            @endphp
            <li>
                <a href="{{ route('notifications.open', $n->id) }}"
                   class="dropdown-item d-flex gap-2 py-2 text-wrap {{ $n->read_at ? '' : 'bg-primary-subtle' }}">
                    <i class="bi {{ $icon }} mt-1"></i>
                    <span>
                        <span class="d-block small {{ $n->read_at ? '' : 'fw-semibold' }}">{{ $n->data['message'] ?? 'Notification' }}</span>
                        <span class="d-block text-muted" style="fontsize: 12px;">{{ $n->created_at->diffForHumans() }}</span>
                    </span>
                </a>
            </li>
        @empty
            <li class="px-3 py-4 text-center text-muted small">No notifications yet.</li>
        @endforelse

        <li class="border-top">
            <a href="{{ route('notifications.index') }}" class="dropdown-item text-center small py-2">View all</a>
        </li>
    </ul>
</div>
@endauth