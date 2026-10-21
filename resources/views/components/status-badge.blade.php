@props(['post'])

@php
    $status = $post->status ?? 'published';
@endphp

@if($status === 'draft')
    <span class="badge bg-secondary-subtle text-secondary-emphasis d-inline-flex align-items-center">
        <i class="bi bi-file-earmark-text me-1"></i> Draft
    </span>
@elseif($status === 'pending')
    <span class="badge bg-warning-subtle text-warning-emphasis d-inline-flex align-items-center">
        <i class="bi bi-hourglass-split me-1"></i> Pending approval
    </span>
@elseif($status === 'rejected')
    <span class="badge bg-danger-subtle text-danger-emphasis d-inline-flex align-items-center">
        <i class="bi bi-x-circle me-1"></i> Rejected
    </span>
@elseif($post->isScheduled())
    <span class="badge bg-info-subtle text-info-emphasis d-inline-flex align-items-center">
        <i class="bi bi-clock me-1"></i> Scheduled · {{ $post->scheduled_at->format('M d, h:i A') }}
    </span>
@else
    <span class="badge bg-success-subtle text-success-emphasis d-inline-flex align-items-center">
        <i class="bi bi-check-circle me-1"></i> Published
    </span>
@endif