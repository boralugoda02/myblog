@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h3 class="fw-bold mb-1">API Tokens</h3>
            <p class="text-muted mb-0">Create tokens so other apps can read your blog through the API.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTokenModal">
            <i class="bi bi-plus-lg me-1"></i> Create token
        </button>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Shown only once, right after a token is created --}}
    @if (session('new_token'))
        <div class="alert alert-success border-0 shadow-sm">
            <div class="fw-semibold mb-2">
                <i class="bi bi-check-circle-fill me-1"></i>
                Token created. Copy it now, it will not be shown again.
            </div>
            <div class="input-group">
                <input type="text" id="newToken" class="form-control font-monospace"
                       value="{{ session('new_token') }}" readonly>
                <button class="btn btn-dark" type="button"
                        onclick="navigator.clipboard.writeText(document.getElementById('newToken').value); this.innerHTML='Copied!';">
                    <i class="bi bi-clipboard me-1"></i> Copy
                </button>
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Created</th>
                        <th>Last used</th>
                        <th>Expires</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tokens as $t)
                        <tr>
                            <td class="ps-4 fw-semibold">
                                <i class="bi bi-key-fill text-primary me-1"></i> {{ $t->name }}
                            </td>
                            <td>{{ $t->created_at->format('M d, Y') }}</td>
                            <td>{{ $t->last_used_at ? $t->last_used_at->diffForHumans() : 'Never' }}</td>
                            <td>
                                @if ($t->expires_at)
                                    {{ $t->expires_at->format('M d, Y') }}
                                    @if ($t->expires_at->isPast())
                                        <span class="badge bg-danger ms-1">Expired</span>
                                    @endif
                                @else
                                    Never
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <form method="POST" action="{{ route('admin.api-tokens.destroy', $t->id) }}"
                                      onsubmit="return confirm('Revoke this token? Apps using it will stop working.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> Revoke
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                No tokens yet. Click "Create token" to make your first one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-1"></i> How to use a token</h6>
            <p class="mb-2">Send it in the header of every request:</p>
            <pre class="mb-3"><code>Accept: application/json
Authorization: Bearer YOUR_TOKEN</code></pre>
            <p class="mb-2">Available endpoints:</p>
            <ul class="mb-0">
                <li><code>GET {{ url('/api/posts') }}</code> (add <code>?search=word</code> or <code>?category_id=1</code>)</li>
                <li><code>GET {{ url('/api/posts/post-slug') }}</code></li>
                <li><code>GET {{ url('/api/categories') }}</code></li>
                <li><code>GET {{ url('/api/me') }}</code></li>
            </ul>
        </div>
    </div>
</div>

{{-- Create token popup --}}
<div class="modal fade" id="createTokenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.api-tokens.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Create API token</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Token name</label>
                    <input type="text" name="name" class="form-control" maxlength="60"
                           placeholder="e.g. Mobile app" value="{{ old('name') }}" required>
                    <div class="form-text">Only to help you remember what this token is for.</div>
                </div>
                <div class="mb-1">
                    <label class="form-label fw-semibold">Expires</label>
                    <select name="expires" class="form-select">
                        <option value="never">Never</option>
                        <option value="30">In 30 days</option>
                        <option value="90">In 90 days</option>
                        <option value="365">In 1 year</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-key me-1"></i> Create token
                </button>
            </div>
        </form>
    </div>
</div>
@endsection