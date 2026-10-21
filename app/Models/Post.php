<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'content', 'image', 'user_id', 'category_id',
        'meta_title', 'meta_description', 'meta_keywords', 'meta_image',
        'status', 'scheduled_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('scheduled_at')
                  ->orWhere('scheduled_at', '<=', now());
            });
    }

    
    public function isLive(): bool
    {
        return $this->status === 'published'
            && (! $this->scheduled_at || $this->scheduled_at->lte(now()));
    }

    
    public function isScheduled(): bool
    {
        return $this->status === 'published'
            && $this->scheduled_at
            && $this->scheduled_at->isFuture();
    }

    public function getReadingTimeAttribute(): int
    {
        $text  = html_entity_decode(strip_tags((string) $this->content));
        $words = preg_match_all('/\S+/u', $text);

        return max(1, (int) ceil($words / 200));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}