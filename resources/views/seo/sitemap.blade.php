{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('home') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('posts.index') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ route('tags.index') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>
@foreach($categories as $category)
    <url>
        <loc>{{ route('posts.index', ['category' => $category->slug ?? $category->id]) }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
@endforeach
@foreach($tags as $tag)
    <url>
        <loc>{{ route('tags.show', $tag->slug) }}</loc>
        <lastmod>{{ $tag->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>
@endforeach
@foreach($posts as $post)
    <url>
        <loc>{{ route('posts.show', $post->slug) }}</loc>
        <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
@endforeach
</urlset>