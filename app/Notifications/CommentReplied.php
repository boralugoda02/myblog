<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class CommentReplied extends Notification
{
    public function __construct(
        public Comment $reply,
        public string $replierName,
        public string $postTitle,
        public string $postSlug,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $excerpt = Str::limit($this->reply->comment, 60);

        return [
            'status'  => 'reply',
            'message' => "{$this->replierName} replied to your comment on \"{$this->postTitle}\": \"{$excerpt}\"",
            
            'url'     => route('posts.show', $this->postSlug, false) . '#comment-' . $this->reply->id,
        ];
    }
}