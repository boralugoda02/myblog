@if($post->tags->isNotEmpty())
    <div class="d-flex flex-wrap gap-2 mb-3">
        @foreach($post->tags as $tag)
            <a href="{{ route('tags.show', $tag->slug) }}"
               class="badge rounded-pill text-bg-light border text-decoration-none fw-normal">#{{ $tag->name }}</a>
        @endforeach
    </div>
@endif