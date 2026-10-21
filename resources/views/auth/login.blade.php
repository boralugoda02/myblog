<x-guest-layout>
    
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-4">
        <h4 class="fw-bold text-white mb-1">Welcome Back!</h4>
        <p class="text-secondary small">Sign in to manage your blog posts</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        
        <div class="mb-3">
            <label for="email" class="form-label text-light small fw-semibold">Email Address</label>
            <input id="email" class="form-control form-control-lg rounded-3 fs-6" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger small" />
        </div>

        
        <div class="mb-3">
            <label for="password" class="form-label text-light small fw-semibold">Password</label>
            <input id="password" class="form-control form-control-lg rounded-3 fs-6" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger small" />
        </div>

        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input id="remember_me" type="checkbox" class="form-check-input bg-dark border-secondary" name="remember">
                <label for="remember_me" class="form-check-label small text-secondary">Remember me</label>
            </div>
            @if (Route::has('password.request'))
                <a class="text-decoration-none small text-primary fw-semibold" href="{{ route('password.request') }}">
                    Forgot password?
                </a>
            @endif
        </div>

        
        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold rounded-3 shadow-sm mb-3">
            Log In
        </button>

        
        <div class="d-flex align-items-center my-3">
            <hr class="flex-grow-1 border-secondary">
            <span class="mx-2 small text-secondary">OR</span>
            <hr class="flex-grow-1 border-secondary">
        </div>

        <a href="{{ route('auth.google') }}"
           class="btn btn-light btn-lg w-100 fw-bold rounded-3 shadow-sm mb-3 d-flex align-items-center justify-content-center gap-2">
            <img src="https://www.google.com/favicon.ico" width="20" alt="G">
            Continue with Google
        </a>
        

        
        <div class="text-center">
            <span class="small text-secondary">Don't have an account?</span>
            <a href="{{ route('register') }}" class="text-decoration-none small fw-bold text-primary ms-1">Register</a>
        </div>
    </form>
</x-guest-layout>