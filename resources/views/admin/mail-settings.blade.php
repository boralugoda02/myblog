@extends('layouts.app')
@section('title', 'Mail Settings')

@section('content')
<div class="container-fluid px-0">

    @include('components.flash-messages')

    <h4 class="fw-bold mb-4">Mail (SMTP) settings</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 p-4">
                <p class="text-muted small">Used for the password reset email and every other email from the site.</p>

                <form action="{{ route('admin.mail.update') }}" method="POST" class="row g-3">
                    @csrf
                    @method('PUT')

                    <div class="col-md-8">
                        <label class="form-label small fw-semibold">SMTP host</label>
                        <input type="text" name="host" class="form-control" placeholder="smtp.gmail.com"
                               value="{{ old('host', $settings['host'] ?? '') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Port</label>
                        <input type="number" name="port" class="form-control" placeholder="587"
                               value="{{ old('port', $settings['port'] ?? 587) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Encryption</label>
                        @php $enc = old('encryption', $settings['encryption'] ?? 'tls'); @endphp
                        <select name="encryption" class="form-select">
                            <option value="tls" {{ $enc === 'tls' ? 'selected' : '' }}>TLS (port 587)</option>
                            <option value="ssl" {{ $enc === 'ssl' ? 'selected' : '' }}>SSL (port 465)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Username</label>
                        <input type="text" name="username" class="form-control" autocomplete="off"
                               value="{{ old('username', $settings['username'] ?? '') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" autocomplete="new-password"
                               placeholder="{{ $hasPassword ? 'Saved. Leave empty to keep it.' : 'SMTP password or app password' }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">From email</label>
                        <input type="email" name="from_address" class="form-control" placeholder="noreply@example.com"
                               value="{{ old('from_address', $settings['from_address'] ?? '') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">From name</label>
                        <input type="text" name="from_name" class="form-control" placeholder="MyBlog"
                               value="{{ old('from_name', $settings['from_name'] ?? config('app.name')) }}" required>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Save settings</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
                <h5 class="fw-bold">Send a test email</h5>
                <p class="text-muted small">Save the settings first, then send a test.</p>

                <form action="{{ route('admin.mail.test') }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <input type="email" name="test_email" class="form-control" placeholder="you@example.com"
                           value="{{ old('test_email', auth()->user()->email) }}" required>
                    <button type="submit" class="btn btn-success text-nowrap">Send test</button>
                </form>
            </div>

            <div class="card border-0 shadow-sm rounded-3 p-4">
                <h6 class="fw-bold">Using Gmail</h6>
                <ul class="small text-muted mb-0 ps-3">
                    <li>Host smtp.gmail.com, port 587, TLS</li>
                    <li>Username: your full Gmail address</li>
                    <li>Password: a Google App Password. Your normal password will not work.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection