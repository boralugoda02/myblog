@php $readonly = $readonly ?? false; @endphp

<form action="{{ $action }}" method="POST">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Name + description --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-semibold small">Role name</label>
                    <input type="text" name="name" maxlength="60" class="form-control rounded-3"
                           value="{{ old('name', $role?->name) }}"
                           placeholder="e.g. Editor" {{ $readonly ? 'disabled' : 'required' }}>
                </div>
                <div class="col-md-7">
                    <label class="form-label fw-semibold small">Description</label>
                    <input type="text" name="description" maxlength="255" class="form-control rounded-3"
                           value="{{ old('description', $role?->description) }}"
                           placeholder="What is this role for?" {{ $readonly ? 'disabled' : '' }}>
                </div>
            </div>
        </div>
    </div>

    @if($readonly)
        <div class="alert alert-info rounded-3">
            <i class="bi bi-info-circle me-2"></i> The Admin role always has every permission and cannot be changed.
        </div>
    @endif

    {{-- Permission groups --}}
    <div class="row g-4 mb-4">
        @foreach($groups as $groupName => $perms)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">{{ $groupName ?: 'General' }}</h6>
                        @unless($readonly)
                            <a href="#" class="small text-decoration-none" onclick="toggleGroup(this); return false;">Select all / none</a>
                        @endunless
                    </div>

                    @foreach($perms as $perm)
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch"
                               name="permissions[]" value="{{ $perm->id }}" id="perm{{ $perm->id }}"
                               {{ ($readonly || in_array((int) $perm->id, array_map('intval', $selected), true)) ? 'checked' : '' }}
                               {{ $readonly ? 'disabled' : '' }}>
                        <label class="form-check-label fw-semibold" for="perm{{ $perm->id }}">{{ $perm->name }}</label>
                        <div class="text-muted small">{{ $perm->slug }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Buttons --}}
    <div class="d-flex gap-2">
        @unless($readonly)
            <button type="submit" class="btn btn-primary rounded-pill px-4">{{ $submitLabel }}</button>
        @endunless
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            {{ $readonly ? 'Back' : 'Cancel' }}
        </a>
    </div>
</form>

<script>
    
    function toggleGroup(link) {
        var boxes = link.closest('.card').querySelectorAll('input[type=checkbox]');
        var allOn = Array.from(boxes).every(function (b) { return b.checked; });
        boxes.forEach(function (b) { b.checked = !allOn; });
    }
</script>