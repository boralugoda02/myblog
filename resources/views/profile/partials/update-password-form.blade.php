<form method="POST" action="{{ route('password.update') }}">
    @csrf
    @method('PUT')

    {{-- Current Password --}}
    <div class="mb-3">
        <label class="form-label fw-semibold small">
            <i class="bi bi-lock me-1"></i> Current Password
        </label>
        <input type="password" name="current_password"
               class="form-control rounded-3 @error('current_password','updatePassword') is-invalid @enderror"
               autocomplete="current-password">
        @error('current_password', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- New Password --}}
    <div class="mb-3">
        <label class="form-label fw-semibold small">
            <i class="bi bi-key me-1"></i> New Password
        </label>
        <input type="password" name="password"
               class="form-control rounded-3 @error('password','updatePassword') is-invalid @enderror"
               autocomplete="new-password">
        @error('password', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Confirm Password --}}
    <div class="mb-4">
        <label class="form-label fw-semibold small">
            <i class="bi bi-key-fill me-1"></i> Confirm Password
        </label>
        <input type="password" name="password_confirmation"
               class="form-control rounded-3"
               autocomplete="new-password">
    </div>

    {{-- Success --}}
    @if(session('status') === 'password-updated')
        <div class="alert alert-success rounded-3 py-2 small">
            <i class="bi bi-check-circle me-1"></i> Password updated successfully!
        </div>
    @endif

    <button type="submit" class="btn btn-warning rounded-pill px-4">
        <i class="bi bi-shield-lock me-1"></i> Update Password
    </button>
</form>