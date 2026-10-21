@php $isReply = $isReply ?? false; @endphp

<div id="comment-{{ $comment->id }}" class="comment-item">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <div>
            <strong>{{ $comment->user->name ?? 'Unknown User' }}</strong>
            <small class="text-muted ms-2">{{ $comment->created_at->diffForHumans() }}</small>
        </div>

        {{-- Reply: any logged-in user. Edit: owner only. Delete: owner or admin (CommentPolicy) --}}
        @auth
            <div>
                <button class="btn btn-sm btn-link p-0 me-2" type="button" data-bs-toggle="collapse" data-bs-target="#replyForm{{ $comment->id }}">
                    <i class="bi bi-reply"></i> Reply
                </button>

                @can('update', $comment)
                    <button class="btn btn-sm btn-link text-warning p-0 me-2" type="button" data-bs-toggle="collapse" data-bs-target="#editComment{{ $comment->id }}">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                @endcan

                @can('delete', $comment)
                    <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="openDeleteModal('{{ route('comments.destroy', $comment->id) }}')">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                @endcan
            </div>
        @endauth
    </div>

    <p class="mb-0">{{ $comment->comment }}</p>

    @can('update', $comment)
        <div class="collapse mt-2" id="editComment{{ $comment->id }}">
            <form action="{{ route('comments.update', $comment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-2">
                    <textarea name="comment" class="form-control form-control-sm" rows="2" required>{{ $comment->comment }}</textarea>
                </div>
                <button type="submit" class="btn btn-sm btn-success">Update</button>
            </form>
        </div>
    @endcan

    @auth
        <div class="collapse mt-2" id="replyForm{{ $comment->id }}">
            <form action="{{ route('comments.store', $post->id) }}" method="POST">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                <div class="mb-2">
                    <textarea name="comment" class="form-control form-control-sm" rows="2" placeholder="Write a reply..." required>{{ $isReply ? '@' . ($comment->user->name ?? '') . ' ' : '' }}</textarea>
                </div>
                <button type="submit" class="btn btn-sm btn-primary">Reply</button>
            </form>
        </div>
    @endauth
</div>