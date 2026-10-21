<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Notifications\NewPostSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function home()
    {
        $featuredPost = Post::published()->latest()->first();

        $popularPosts = Post::published()->orderBy('views', 'desc')->take(4)->get();

        $categories = Category::withCount(['posts' => fn ($q) => $q->published()])->get();

        $latestPosts = Post::published()->when($featuredPost, function ($query) use ($featuredPost) {
            return $query->where('id', '!=', $featuredPost->id);
        })->latest()->paginate(6);

        return view('home', compact('featuredPost', 'popularPosts', 'categories', 'latestPosts'));
    }

    public function dashboard(Request $request)
    {
        $query = Post::with(['category', 'user']);

        if (! auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('category', function($catQuery) use ($search) {
                      $catQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $posts = $query->oldest()->get();
        $categoriesCount = Category::count();

        return view('dashboard', compact('posts', 'categoriesCount'));
    }

    public function index(Request $request)
    {
        $query = Post::published()->with(['user', 'category']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('category', function($catQuery) use ($search) {
                      $catQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('category')) {
            $category = $request->category;
            $query->whereHas('category', function($q) use ($category) {
                $q->where('slug', $category)->orWhere('id', $category);
            });
        }

        $posts = $query->oldest()->paginate(9);
        $categories = Category::all();

        return view('posts.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $this->authorize('create', Post::class);

        $categories = Category::all();
        return view('posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        
        $isDraft = $request->input('action') === 'draft';

        $request->validate([
            'title'            => 'required|max:255',
            'category_id'      => 'required|exists:categories,id',
            'content'          => $isDraft ? 'nullable' : 'required',
            'scheduled_at'     => 'nullable|date|after:now',
            'tags'             => 'nullable|string|max:255',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:20480',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:255',
            'meta_image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $metaImagePath = null;
        if ($request->hasFile('meta_image')) {
            $metaImagePath = $request->file('meta_image')->store('meta', 'public');
        }

        $post = Post::create([
            'title'            => $request->title,
            'slug'             => Str::slug($request->title) . '-' . time(),

            'status'           => $isDraft ? 'draft' : $this->submitStatus(),
            'scheduled_at'     => $request->filled('scheduled_at') ? $request->scheduled_at : null,
            'category_id'      => $request->category_id,
            
            'content'          => clean($request->content ?? '', 'blog'),
            'image'            => $imagePath,
            'user_id'          => auth()->id(),
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords'    => $request->meta_keywords,
            'meta_image'       => $metaImagePath,
        ]);

        if ($request->has('tags')) {
            $this->syncTags($post, $request->tags);
        }

        if ($post->status === 'pending') {
            $this->notifyAdmins($post);
        }

        $message = match (true) {
            $post->status === 'draft'   => 'Draft saved! You can edit and submit it later.',
            $post->status === 'pending' => 'Post submitted! It will be visible after admin approval.',
            $post->isScheduled()        => 'Post scheduled successfully!',
            default                     => 'Post published successfully!',
        };

        return redirect()->route('dashboard')->with('success', $message);
    }

    public function show(Post $post)
    {
        $this->authorize('view', $post);

        
        if ($post->isLive() && auth()->id() !== $post->user_id) {
            $key = 'viewed_post_' . $post->id;

            if (! session()->has($key)) {
                $post->increment('views');
                session([$key => true]);
            }
        }

                
        $post->load([
            'category', 'user', 'tags',
            'comments' => fn ($q) => $q->whereNull('parent_id')->orderBy('id')->with(['user', 'replies.user']),
        ]);

        
        $related = Post::published()
            ->with('category')
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(3)
            ->get();

        
        $popular = Post::published()
            ->where('id', '!=', $post->id)
            ->orderByDesc('views')
            ->take(5)
            ->get();

        return view('posts.show', compact('post', 'related', 'popular'));
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        $post->load('tags');

        $categories = Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $isDraft = $request->input('action') === 'draft';

        $request->validate([
            'title'            => 'required|max:255',
            'category_id'      => 'required|exists:categories,id',
            'content'          => $isDraft ? 'nullable' : 'required',
            'scheduled_at'     => 'nullable|date',
            'tags'             => 'nullable|string|max:255',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:255',
            'meta_image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $post->image = $request->file('image')->store('posts', 'public');
        }

        if ($request->hasFile('meta_image')) {
            if ($post->meta_image) {
                Storage::disk('public')->delete($post->meta_image);
            }
            $post->meta_image = $request->file('meta_image')->store('meta', 'public');
        }

        $data = [
            'title'            => $request->title,
            'category_id'      => $request->category_id,
            'content'          => clean($request->content ?? '', 'blog'),
            'scheduled_at'     => $request->filled('scheduled_at') ? $request->scheduled_at : null,
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords'    => $request->meta_keywords,
        ];

        
        $message = 'Post updated successfully!';
        $submitted = false;

        if (in_array($post->status, ['draft', 'rejected'], true)) {
            $data['status'] = $isDraft ? 'draft' : $this->submitStatus();
            $submitted = ! $isDraft;

            $message = $isDraft
                ? 'Draft saved!'
                : ($data['status'] === 'pending'
                    ? 'Post submitted! It will be visible after admin approval.'
                    : 'Post published successfully!');
        }

        $post->update($data);

        if ($request->has('tags')) {
            $this->syncTags($post, $request->tags);
        }

        if ($submitted && $post->status === 'pending') {
            $this->notifyAdmins($post);
        }

        return redirect()->route('dashboard')->with('success', $message);
    }

    
    public function submit(Post $post)
    {
        $this->authorize('update', $post);

        if (! in_array($post->status, ['draft', 'rejected'], true)) {
            return redirect()->route('dashboard');
        }

        if (blank(strip_tags((string) $post->content))) {
            return redirect()->route('posts.edit', $post->id)
                ->withErrors(['content' => 'Content ekak add karala passe submit karanna.']);
        }

        $post->update(['status' => $this->submitStatus()]);

        if ($post->status === 'pending') {
            $this->notifyAdmins($post);
        }

        return redirect()->route('dashboard')->with('success', $post->status === 'pending'
            ? 'Post submitted! It will be visible after admin approval.'
            : 'Post published successfully!');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('dashboard')->with('success', 'Post moved to trash successfully!');
    }

    public function trash()
    {
        $query = Post::onlyTrashed()->with('category');

        if (! auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $trashedPosts = $query->latest()->get();
        return view('posts.trash', compact('trashedPosts'));
    }

    public function restore($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $post);

        $post->restore();

        return redirect()->route('posts.trash')->with('success', 'Blog post restored successfully!');
    }

    public function forceDelete($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $post);

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        if ($post->meta_image) {
            Storage::disk('public')->delete($post->meta_image);
        }

        $post->forceDelete();

        return redirect()->route('posts.trash')->with('success', 'Blog post permanently deleted!');
    }

    
    
    private function submitStatus(): string
    {
        return auth()->user()->isAdmin() ? 'published' : 'pending';
    }

    private function notifyAdmins(Post $post): void
    {
        try {
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new NewPostSubmitted($post, auth()->user()->name));
        } catch (\Throwable $e) {
            report($e); 
        }
    }

    private function syncTags(Post $post, ?string $input): void
    {
        $ids = collect(explode(',', (string) $input))
            ->map(fn ($name) => trim(ltrim(trim($name), '#')))
            ->filter()
            ->map(fn ($name) => mb_substr($name, 0, 40))
            ->unique(fn ($name) => mb_strtolower($name))
            ->take(10)
            ->map(function ($name) {
                $slug = trim(preg_replace('/[^\p{L}\p{M}\p{N}]+/u', '-', mb_strtolower($name)), '-');

                if ($slug === '') {
                    return null;
                }

                return Tag::firstOrCreate(['slug' => $slug], ['name' => $name])->id;
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        $post->tags()->sync($ids);
    }
}