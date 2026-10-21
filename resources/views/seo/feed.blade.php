{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>MyBlog</title>
        <link>{{ route('home') }}</link>
        <description>Latest posts from MyBlog</description>
        <language>en</language>
        <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
        <atom:link href="{{ route('feed') }}" rel="self" type="application/rss+xml" />
@foreach($posts as $post)
        <item>
            <title>{{ $post->title }}</title>
            <link>{{ route('posts.show', $post->slug) }}</link>
            <guid isPermaLink="true">{{ route('posts.show', $post->slug) }}</guid>
            <pubDate>{{ $post->created_at->toRssString() }}</pubDate>
@if($post->category)
            <category>{{ $post->category->name }}</category>
@endif
            <description>{{ \Illuminate\Support\Str::limit(html_entity_decode(strip_tags($post->content)), 300) }}</description>
        </item>
@endforeach
    </channel>
</rss>