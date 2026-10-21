<x-guest-layout>
    <div class="text-center mb-4">
        <h4 class="fw-bold text-white mb-1">Create Account</h4>
        <p class="text-secondary small">Join MyBlog and share your stories</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        
        <div class="mb-3">
            <label for="name" class="form-label text-light small fw-semibold">Full Name</label>
            <input id="name" class="form-control form-control-lg rounded-3 fs-6" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="John Doe">
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-danger small" />
        </div>

        
        <div class="mb-3">
            <label for="email" class="form-label text-light small fw-semibold">Email Address</label>
            <input id="email" class="form-control form-control-lg rounded-3 fs-6" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="name@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger small" />
        </div>

        
        <div class="mb-3">
            <label for="password" class="form-label text-light small fw-semibold">Password</label>
            <input id="password" class="form-control form-control-lg rounded-3 fs-6" type="password" name="password" required autocomplete="new-password" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger small" />
        </div>

        
        <div class="mb-4">
            <label for="password_confirmation" class="form-label text-light small fw-semibold">Confirm Password</label>
            <input id="password_confirmation" class="form-control form-control-lg rounded-3 fs-6" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-danger small" />
        </div>

        
        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold rounded-3 shadow-sm mb-3">
            Register
        </button>

        
        <div class="text-center">
            <span class="small text-secondary">Already registered?</span>
            <a href="{{ route('login') }}" class="text-decoration-none small fw-bold text-primary ms-1">Log In</a>
        </div>
    </form>
</x-guest-layout>