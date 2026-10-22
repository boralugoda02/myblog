<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::published()
            ->with(['user', 'category'])
            ->when($request->category_id, fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->search, function ($q, $s) {
                $q->where(fn ($q) => $q->where('title', 'like', "%{$s}%")
                                       ->orWhere('content', 'like', "%{$s}%"));
            })
            ->latest()
            ->paginate(10);

        return PostResource::collection($posts);
    }

    public function show(string $slug)
    {
        $post = Post::published()
            ->with(['user', 'category'])
            ->where('slug', $slug)
            ->firstOrFail();

        return new PostResource($post);
    }
}