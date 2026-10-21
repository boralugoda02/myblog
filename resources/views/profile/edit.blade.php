@extends('layouts.app')
@section('title', 'My Profile')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">👤 My Profile</h4>
        <small class="text-muted">Manage your account settings</small>
    </div>
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
</div>

<div class="row g-4">

    {{-- Left Column - Avatar + Info --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 text-center p-4">

            {{-- Avatar --}}
            <div class="position-relative d-inline-block mx-auto mb-3">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}"
                         class="rounded-circle border border-3 border-primary"
                         width="100" height="100" style="object-fit:cover;">
                @else
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto fw-bold"
                         style="width:100px;height:100px;font-size:36px;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <h5 class="fw-bold mb-1">{{ auth()->user()->name }}</h5>
            <p class="text-muted small mb-2">{{ auth()->user()->email }}</p>

            {{-- Role Badge --}}
            @if(auth()->user()->role === 'admin')
                <span class="badge bg-danger rounded-pill px-3 mb-3">
                    <i class="bi bi-shield-check me-1"></i>Admin
                </span>
            @elseif(auth()->user()->role === 'author')
                <span class="badge bg-success rounded-pill px-3 mb-3">
                    <i class="bi bi-pencil me-1"></i>Author
                </span>
            @else
                <span class="badge bg-secondary rounded-pill px-3 mb-3">
                    <i class="bi bi-person me-1"></i>User
                </span>
            @endif

            <hr>

            {{-- Stats --}}
            <div class="row g-2 text-center">
                <div class="col-6">
                    <div class="fw-bold fs-5 text-primary">
                        {{ auth()->user()->posts()->count() }}
                    </div>
                    <small class="text-muted">Posts</small>
                </div>
                <div class="col-6">
                    <div class="fw-bold fs-5 text-success">
                        {{ auth()->user()->posts()->where('status','published')->count() }}
                    </div>
                    <small class="text-muted">Published</small>
                </div>
                <div class="col-6">
                    <div class="fw-bold fs-5 text-warning">
                        {{ auth()->user()->posts()->where('status','pending')->count() }}
                    </div>
                    <small class="text-muted">Pending</small>
                </div>
                <div class="col-6">
                    <div class="fw-bold fs-5 text-secondary">
                        {{ auth()->user()->created_at->format('M Y') }}
                    </div>
                    <small class="text-muted">Joined</small>
                </div>
            </div>

            @if(auth()->user()->google_id)
                <hr>
                <small class="text-muted">
                    <i class="bi bi-google me-1"></i> Signed in with Google
                </small>
            @endif

        </div>
    </div>

    {{-- Right Column - Forms --}}
    <div class="col-md-8">

        {{-- Update Profile Info --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-person-gear me-2 text-primary"></i>
                    Profile Information
                </h6>
                <small class="text-muted">Update your name and email address</small>
            </div>
            <div class="card-body px-4 pb-4">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- Update Password --}}
        @if(!auth()->user()->google_id)
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-lock me-2 text-warning"></i>
                    Update Password
                </h6>
                <small class="text-muted">Use a strong password to keep your account secure</small>
            </div>
            <div class="card-body px-4 pb-4">
                @include('profile.partials.update-password-form')
            </div>
        </div>
        @else
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body px-4 py-4 text-center text-muted">
                <i class="bi bi-google fs-3 mb-2 d-block"></i>
                <small>Google account වලට password change කරන්න බැහැ.</small>
            </div>
        </div>
        @endif

        {{-- Danger Zone - Delete Account --}}
        {{-- Danger Zone --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h6 class="fw-bold mb-0 text-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Danger Zone
        </h6>
        <small class="text-muted">Permanently delete your account</small>
    </div>
    <div class="card-body px-4 pb-4">
        @include('profile.partials.delete-user-form')
    </div>
</div>

    </div>
</div>

@endsection