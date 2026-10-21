<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Role; // <-- NEW
use App\Models\User;
use App\Notifications\PostStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule; // <-- NEW

class AdminDashboardController extends Controller
{
    public function index()
    {
        $pendingCount   = Post::where('status', 'pending')->count();
        $publishedCount = Post::where('status', 'published')->count();
        $rejectedCount  = Post::where('status', 'rejected')->count();

        $usersCount      = User::count();
        $categoriesCount = Category::count();
        $trashCount      = Post::onlyTrashed()->count();

        $tagsCount     = Schema::hasTable('tags') ? DB::table('tags')->count() : 0;
        $commentsCount = Schema::hasTable('comments') ? DB::table('comments')->count() : 0;

        $pendingPosts = Post::with(['user', 'category'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'pendingCount', 'publishedCount', 'rejectedCount', 'usersCount',
            'categoriesCount', 'trashCount', 'tagsCount', 'commentsCount', 'pendingPosts'
        ));
    }

    public function posts(string $status)
    {
        $posts = Post::with(['user', 'category'])
            ->where('status', $status)
            ->latest()
            ->paginate(10);

        return view('admin.posts', compact('posts', 'status'));
    }

    public function users()
    {
        $users = User::latest()->paginate(10);

        
        $roles = Role::orderBy('id')->get();

        return view('admin.users', compact('users', 'roles'));
    }

    public function updateRole(Request $request, User $user)
    {
        Gate::authorize('manage-users');

        
        $data = $request->validate([
            'role' => ['required', Rule::exists('roles', 'slug')],
        ]);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->role = $data['role'];
        $user->save();

        $roleName = Role::where('slug', $data['role'])->value('name') ?? ucfirst($data['role']);

        return back()->with('success', "{$user->name} is now {$roleName}.");
    }


    public function deleteUser(User $user)
    {
        Gate::authorize('manage-users');


        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }


        if ($user->role === 'admin') {
            return back()->with('error', 'Admin accounts cannot be deleted!');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "{$name} has been deleted successfully.");
    }

    public function approve(Post $post)
    {
        $alreadyPublished = $post->status === 'published';

        $post->status = 'published';
        $post->save();

        if (! $alreadyPublished) {
            $this->notifyAuthor($post, 'published');
        }

        return back()->with('success', 'Post approved and published.');
    }

    public function reject(Request $request, Post $post)
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $alreadyRejected = $post->status === 'rejected';

        $post->status = 'rejected';
        $post->save();

        if (! $alreadyRejected) {
            $this->notifyAuthor($post, 'rejected', $data['reason'] ?? null);
        }

        return back()->with('success', 'Post rejected.');
    }

    private function notifyAuthor(Post $post, string $status, ?string $reason = null): void
    {
        $author = $post->user;

        if (! $author || $author->id === auth()->id()) {
            return;
        }

        try {
            $author->notify(new PostStatusChanged($post, $status, $reason));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}