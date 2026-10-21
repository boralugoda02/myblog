@extends('layouts.app')
@section('title', 'Manage Posts')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0 text-capitalize">{{ $status }} posts</h4>
            <small class="text-muted">{{ $posts->total() }} {{ \Illuminate\Support\Str::plural('post', $posts->total()) }}</small>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left"></i> Back to dashboard
        </a>
    </div>

    <div class="d-flex gap-2 mb-3">
        <a href="{{ route('admin.posts.status', 'pending') }}" class="btn btn-sm rounded-pill {{ $status === 'pending' ? 'btn-primary' : 'btn-light shadow-sm' }}">Pending</a>
        <a href="{{ route('admin.posts.status', 'published') }}" class="btn btn-sm rounded-pill {{ $status === 'published' ? 'btn-primary' : 'btn-light shadow-sm' }}">Published</a>
        <a href="{{ route('admin.posts.status', 'rejected') }}" class="btn btn-sm rounded-pill {{ $status === 'rejected' ? 'btn-primary' : 'btn-light shadow-sm' }}">Rejected</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Post</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-3">
                                    @if($post->image)
                                        <img src="{{ asset('storage/' . $post->image) }}" width="48" height="48" class="rounded" style="object-fit: cover;" alt="">
                                    @else
                                        <div class="rounded bg-secondary-subtle" style="width:48px;height:48px;"></div>
                                    @endif
                                    <span class="fw-semibold">{{ \Illuminate\Support\Str::limit($post->title, 50) }}</span>
                                </div>
                            </td>
                            <td>{{ $post->user->name ?? 'Unknown' }}</td>
                            <td>
                                @if($post->category)
                                    <span class="badge bg-primary-subtle text-primary">{{ $post->category->name }}</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $post->created_at->format('M d, Y') }}</td>
                            <td class="text-end pe-3">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('posts.show', $post) }}" class="btn btn-outline-secondary btn-sm rounded-pill">View</a>

                                    @if($status !== 'published')
                                        <form method="POST" action="{{ route('admin.posts.approve', $post) }}">
                                            @csrf
                                            <button class="btn btn-success btn-sm rounded-pill px-3">Approve</button>
                                        </form>
                                    @endif

                                    @if($status !== 'rejected')
                                        {{-- Opens the reject popup. The author sees the reason in the notification --}}
                                        <button type="button"
                                                class="btn btn-danger btn-sm rounded-pill px-3"
                                                data-url="{{ route('admin.posts.reject', $post) }}"
                                                data-title="{{ $post->title }}"
                                                onclick="openReject(this)">
                                            Reject
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1"></i>
                                <div class="mt-2">No {{ $status }} posts.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $posts->links('pagination::bootstrap-5') }}</div>


    {{-- ===== Reject popup ===== --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" id="rejectForm" class="modal-content border-0 shadow"
                  style="background:var(--bg-card);color:var(--text-primary);border-radius:16px;">
                @csrf

                <div class="modal-body text-center p-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                         style="width:64px;height:64px;background:#fee2e2;color:#dc2626;font-size:1.8rem;">
                        <i class="bi bi-x-lg"></i>
                    </div>

                    <h5 class="fw-bold mb-1">Reject this post?</h5>
                    <p class="text-secondary small mb-3" id="rejectPostTitle"></p>

                    <div class="text-start">
                        <label class="form-label small fw-semibold">
                            Reason <span class="text-secondary fw-normal">(optional, the author will see this)</span>
                        </label>

                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="setReason('Please add more details.')">More details</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="setReason('Please add a featured image.')">Add image</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="setReason('Please fix spelling and grammar.')">Fix grammar</button>
                        </div>

                        <textarea name="reason" id="rejectReason" class="form-control" rows="3"
                                  maxlength="500" placeholder="Write a short reason..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 px-4 pb-4 d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary flex-fill"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger flex-fill">
                        <i class="bi bi-x-circle me-1"></i> Reject post
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        
        function openReject(btn) {
            document.getElementById('rejectForm').action = btn.dataset.url;
            document.getElementById('rejectPostTitle').textContent = btn.dataset.title;
            document.getElementById('rejectReason').value = '';
            new bootstrap.Modal(document.getElementById('rejectModal')).show();
        }

        
        function setReason(text) {
            document.getElementById('rejectReason').value = text;
        }
    </script>
    @endpush

@endsection