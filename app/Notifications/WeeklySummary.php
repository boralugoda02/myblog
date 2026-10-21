<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklySummary extends Notification
{
    public function __construct(public array $report) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $r     = $this->report;
        $first = explode(' ', trim($notifiable->name))[0];

        $mail = (new MailMessage)
            ->subject(config('app.name') . ' weekly summary: ' . $r['from'] . ' - ' . $r['to'])
            ->greeting('Hi ' . $first . ', here is your week (' . $r['from'] . ' - ' . $r['to'] . ')')
            ->line(' Posts submitted: ' . $r['submitted'])
            ->line(' Approved: ' . $r['approved'])
            ->line(' Rejected: ' . $r['rejected'])
            ->line(' Deleted: ' . ($r['deleted'] ?? 'n/a'))
            ->line(' New users: ' . $r['new_users'] . ' (total ' . $r['total_users'] . ')')
            ->line(' New comments: ' . $r['new_comments']);

        // Needs your attention
        if ($r['pending_count'] > 0) {
            $mail->line(' Waiting for your approval: ' . $r['pending_count']
                . ' (the oldest has waited ' . $r['oldest_waiting'] . ')');
        } else {
            $mail->line(' No posts are waiting for approval.');
        }

        if (! empty($r['top_posts'])) {
            $mail->line(' Most viewed posts (all time):');
            foreach ($r['top_posts'] as $i => $p) {
                $mail->line(($i + 1) . '. "' . $p['title'] . '" (' . number_format($p['views']) . ' views)');
            }
        }

        if (! empty($r['upcoming'])) {
            $mail->line(' Scheduled for the coming week:');
            foreach ($r['upcoming'] as $p) {
                $mail->line('• "' . $p['title'] . '" on ' . $p['when']);
            }
        } else {
            $mail->line(' Nothing is scheduled for the coming week.');
        }

        if ($r['no_meta'] !== null || $r['no_image'] !== null) {
            $parts = [];
            if ($r['no_meta'] !== null)  { $parts[] = $r['no_meta'] . ' without meta description'; }
            if ($r['no_image'] !== null) { $parts[] = $r['no_image'] . ' without featured image'; }
            $mail->line(' Quick fixes (published posts): ' . implode(', ', $parts));
        }

        return $mail->action(
            $r['pending_count'] > 0 ? 'Review pending posts' : 'Open admin dashboard',
            $r['pending_count'] > 0 ? url('/admin-panel/posts/status/pending') : url('/admin-panel/dashboard')
        );
    }
}