<?php

namespace App\Http\Controllers;

use App\Models\Tag;

class TagController extends Controller
{

    public function index()
    {
        $tags = Tag::withCount(['posts' => fn ($q) => $q->published()])
            ->having('posts_count', '>', 0)
            ->orderByDesc('posts_count')
            ->get();

        return view('tags.index', compact('tags'));
    }

    
    public function show(Tag $tag)
    {
        $posts = $tag->posts()
            ->published()
            ->with(['user', 'category'])
            ->latest()
            ->paginate(9);

        return view('tags.show', compact('tag', 'posts'));
    }
}