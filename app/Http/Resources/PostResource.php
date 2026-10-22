<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'excerpt'      => Str::limit(strip_tags($this->content), 150),
            'content'      => $this->when($request->routeIs('api.posts.show'), $this->content),
            'image'        => $this->image ? asset('storage/' . $this->image) : null,
            'views'        => (int) $this->views,
            'category'     => $this->category ? [
                'id'   => $this->category->id,
                'name' => $this->category->name,
            ] : null,
            'author'       => $this->user?->name,
            'published_at' => $this->created_at?->toDateString(),
        ];
    }
}