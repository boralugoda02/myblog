<form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('PATCH')

    {{-- Name --}}
    <div class="mb-3">
        <label class="form-label fw-semibold small">
            <i class="bi bi-person me-1"></i> Full Name
        </label>
        <input type="text" name="name"
               class="form-control rounded-3 @error('name') is-invalid @enderror"
               value="{{ old('name', auth()->user()->name) }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Email --}}
    <div class="mb-4">
        <label class="form-label fw-semibold small">
            <i class="bi bi-envelope me-1"></i> Email Address
        </label>
        <input type="email" name="email"
               class="form-control rounded-3 @error('email') is-invalid @enderror"
               value="{{ old('email', auth()->user()->email) }}" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    

    {{-- Success --}}
    @if(session('status') === 'profile-updated')
        <div class="alert alert-success rounded-3 py-2 small">
            <i class="bi bi-check-circle me-1"></i> Profile updated successfully!
        </div>
    @endif

    <button type="submit" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-save me-1"></i> Save Changes
    </button>
</form>