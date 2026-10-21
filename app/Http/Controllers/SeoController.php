<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;

class SeoController extends Controller
{
    public function sitemap()
    {
        $posts      = Post::published()->latest('updated_at')->get(['slug', 'updated_at']);
        $tags       = Tag::whereHas('posts', fn ($q) => $q->published())->get(['slug', 'updated_at']);
        $categories = Category::all();

        return response()
            ->view('seo.sitemap', compact('posts', 'tags', 'categories'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function feed()
    {
        $posts = Post::published()->with('category')->latest()->take(20)->get();

        return response()
            ->view('seo.feed', compact('posts'))
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }

    public function robots()
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin-panel',
            'Disallow: /dashboard',
            'Disallow: /profile',
            'Disallow: /notifications',
            '',
            'Sitemap: ' . route('sitemap'),
        ];

        return response(implode("\n", $lines), 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}