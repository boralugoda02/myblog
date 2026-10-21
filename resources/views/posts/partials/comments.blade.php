@php $totalComments = $post->comments()->count(); @endphp

<style>
    .comment-item:target { outline: 2px solid #0d6efd; outline-offset: 4px; border-radius: 6px; }
</style>

@auth
    <form action="{{ route('comments.store', $post->id) }}" method="POST" class="mt-3">
        @csrf
        <div class="mb-2">
            <textarea name="comment" class="form-control @error('comment') is-invalid @enderror" rows="2" placeholder="Write a comment..." required>{{ old('comment') }}</textarea>
            @error('comment')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Add Comment</button>
    </form>
@else
    <p class="text-muted mb-0">
        <a href="{{ route('login') }}">Log in</a> to write a comment.
    </p>
@endauth

<div class="mt-4">
    <h5>Comments ({{ $totalComments }})</h5>

    @foreach($post->comments as $comment)
        <div class="card my-2 p-3">
            @include('posts.partials.comment-item', ['comment' => $comment, 'isReply' => false])

            @foreach($comment->replies as $reply)
                <div class="ms-3 ms-md-4 mt-3 ps-3 border-start">
                    @include('posts.partials.comment-item', ['comment' => $reply, 'isReply' => true])
                </div>
            @endforeach
        </div>
    @endforeach
</div>