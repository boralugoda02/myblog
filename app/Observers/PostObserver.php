<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Post;
use Illuminate\Support\Str;

class PostObserver
{
    
    private function who(): string
    {
        return auth()->user()->name ?? 'System';
    }

    
    private function title(Post $post): string
    {
        return '"' . Str::limit($post->title, 60) . '"';
    }

    public function created(Post $post): void
    {
        ActivityLog::record(
            'post.created',
            $this->who() . ' created post ' . $this->title($post) . ' (' . $post->status . ')',
            $post
        );
    }

    public function updated(Post $post): void
    {
        
        $changed = array_diff(array_keys($post->getChanges()), ['views', 'updated_at']);

        if (empty($changed)) {
            return;
        }

        
        if (in_array('status', $changed, true)) {
            [$action, $text] = match ($post->status) {
                'published' => ['post.approved',  'approved'],
                'rejected'  => ['post.rejected',  'rejected'],
                'pending'   => ['post.submitted', 'submitted for approval'],
                default     => ['post.updated',   'changed the status to ' . $post->status . ' for'],
            };

            ActivityLog::record(
                $action,
                $this->who() . ' ' . $text . ' post ' . $this->title($post),
                $post
            );
            return;
        }

        
        ActivityLog::record(
            'post.updated',
            $this->who() . ' edited post ' . $this->title($post),
            $post
        );
    }

    
    public function deleted(Post $post): void
    {
        
        if (method_exists($post, 'isForceDeleting') && $post->isForceDeleting()) {
            return;
        }

        ActivityLog::record(
            'post.deleted',
            $this->who() . ' moved post ' . $this->title($post) . ' to trash',
            $post
        );
    }

    public function restored(Post $post): void
    {
        ActivityLog::record(
            'post.restored',
            $this->who() . ' restored post ' . $this->title($post) . ' from trash',
            $post
        );
    }

    public function forceDeleted(Post $post): void
    {
        ActivityLog::record(
            'post.force_deleted',
            $this->who() . ' permanently deleted post ' . $this->title($post),
            $post
        );
    }
}