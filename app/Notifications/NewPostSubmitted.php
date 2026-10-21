<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Notifications\Notification;

class NewPostSubmitted extends Notification
{
    public function __construct(
        public Post $post,
        public string $authorName,
    ) {}

    
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'post_id'    => $this->post->id,
            'post_title' => $this->post->title,
            'status'     => 'pending',
            'message'    => "New post \"{$this->post->title}\" by {$this->authorName} is waiting for your approval.",
        ];
    }
}