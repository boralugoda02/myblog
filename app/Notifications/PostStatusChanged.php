<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostStatusChanged extends Notification
{
    public function __construct(
        public Post $post,
        public string $status,        
        public ?string $reason = null,
    ) {}

    public function via(object $notifiable): array
    {
        
        return ['database', 'mail'];
    }

    protected function message(): string
    {
        return $this->status === 'published'
            ? "Your post \"{$this->post->title}\" was approved and is now live."
            : "Your post \"{$this->post->title}\" was rejected by the admin.";
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->status === 'published' ? 'Your post has been approved' : 'Your post was rejected')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line($this->message());

        if ($this->status === 'rejected' && $this->reason) {
            $mail->line('Reason: ' . $this->reason);
        }

        if ($this->status === 'published' && $this->post->slug) {
            return $mail->action('View your post', route('posts.show', $this->post->slug));
        }

        return $mail->action('Go to your dashboard', route('dashboard'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'post_id'    => $this->post->id,
            'post_title' => $this->post->title,
            'post_slug'  => $this->post->slug,
            'status'     => $this->status,
            'reason'     => $this->reason,
            'message'    => $this->message(),
        ];
    }
}