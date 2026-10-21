<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\WeeklySummary;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SendWeeklySummary extends Command
{
    protected $signature = 'blog:weekly-summary
                            {--days=7 : How many days back to look}
                            {--dry-run : Only show the summary, send nothing}';

    protected $description = 'Email every admin a weekly summary: posts, approvals, rejections, deletions, users, comments, SEO';

    public function handle(): int
    {
        $days  = max(1, (int) $this->option('days'));
        $since = now()->subDays($days);

        
        $submitted = Post::where('status', '!=', 'draft')->where('created_at', '>=', $since)->count();

        
        $approveCol = Schema::hasColumn('posts', 'approved_at') ? 'approved_at' : 'updated_at';
        $approved   = Post::where('status', 'published')->where($approveCol, '>=', $since)->count();

        
        $rejectCol = Schema::hasColumn('posts', 'rejected_at') ? 'rejected_at' : 'updated_at';
        $rejected  = Post::where('status', 'rejected')->where($rejectCol, '>=', $since)->count();

        
        $usesTrash = in_array(SoftDeletes::class, class_uses_recursive(Post::class));
        $deleted   = $usesTrash
            ? Post::onlyTrashed()->where('deleted_at', '>=', $since)->count()
            : null;

        $pending = Post::where('status', 'pending')->oldest()->get();

        
        $upcoming = Post::where('status', 'published')
            ->where('scheduled_at', '>', now())
            ->where('scheduled_at', '<=', now()->addDays(7))
            ->orderBy('scheduled_at')
            ->take(5)
            ->get();

        $report = [
            'from'           => $since->format('M d'),
            'to'             => now()->format('M d'),
            'submitted'      => $submitted,
            'approved'       => $approved,
            'rejected'       => $rejected,
            'deleted'        => $deleted,
            'new_users'      => User::where('created_at', '>=', $since)->count(),
            'total_users'    => User::count(),
            'new_comments'   => Comment::where('created_at', '>=', $since)->count(),
            'pending_count'  => $pending->count(),
            'oldest_waiting' => $pending->first()?->created_at->diffForHumans(null, true),
            
            'top_posts'      => Post::published()->orderByDesc('views')->take(3)->get(['title', 'views'])
                ->map(fn ($p) => ['title' => Str::limit($p->title, 50), 'views' => (int) $p->views])->all(),
            'upcoming'       => $upcoming->map(fn ($p) => [
                'title' => Str::limit($p->title, 50),
                'when'  => Carbon::parse($p->scheduled_at)->format('D, M d g:i A'),
            ])->all(),
            
            'no_meta'        => Schema::hasColumn('posts', 'meta_description')
                ? Post::published()->where(fn ($q) => $q->whereNull('meta_description')->orWhere('meta_description', ''))->count()
                : null,
            'no_image'       => Schema::hasColumn('posts', 'image')
                ? Post::published()->where(fn ($q) => $q->whereNull('image')->orWhere('image', ''))->count()
                : null,
        ];

        $this->info("Weekly summary: {$report['from']} - {$report['to']}");
        $this->table(['Item', 'Value'], [
            ['Posts submitted', $report['submitted']],
            ['Approved', $report['approved']],
            ['Rejected', $report['rejected']],
            ['Deleted', $report['deleted'] ?? 'n/a'],
            ['New users', $report['new_users'] . ' (total ' . $report['total_users'] . ')'],
            ['New comments', $report['new_comments']],
            ['Waiting for approval', $report['pending_count']],
            ['Scheduled (next 7 days)', count($report['upcoming'])],
            ['No meta description', $report['no_meta'] ?? 'n/a'],
            ['No featured image', $report['no_image'] ?? 'n/a'],
        ]);

        foreach ($report['top_posts'] as $i => $p) {
            $this->line('  ' . ($i + 1) . ". {$p['title']} ({$p['views']} views)");
        }

        if ($this->option('dry-run')) {
            $this->warn('Dry run: nothing was sent.');
            return self::SUCCESS;
        }

        $admins = User::where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            $this->error('There are no admins to send this to.');
            return self::FAILURE;
        }

        $failed = 0;

        foreach ($admins as $admin) {
            try {
                $admin->notify(new WeeklySummary($report));
                $this->line("  sent to {$admin->name} <{$admin->email}>");
            } catch (\Throwable $e) {
                report($e);
                $failed++;
                $this->error("  email problem for {$admin->name}: " . $e->getMessage());
            }
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}