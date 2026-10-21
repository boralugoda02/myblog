<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Notifications\CommentReplied;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, $postId)
    {
        $data = $request->validate([
            'comment'   => 'required|string|max:1000',
            'parent_id' => 'nullable|integer|exists:comments,id',
        ]);

        $post = Post::findOrFail($postId);

        $parent = null;
        $rootId = null;

        if (! empty($data['parent_id'])) {
            
            $parent = Comment::where('post_id', $post->id)->find($data['parent_id']);
            abort_if(! $parent, 422, 'Invalid comment to reply to.');

            
            $rootId = $parent->parent_id ?: $parent->id;
        }

        $comment = Comment::create([
            'user_id'   => auth()->id(),
            'post_id'   => $post->id,
            'parent_id' => $rootId,
            'comment'   => $data['comment'],
        ]);

        
        if ($parent && $parent->user_id !== auth()->id() && $parent->user) {
            try {
                $parent->user->notify(new CommentReplied(
                    $comment,
                    auth()->user()->name,
                    $post->title,
                    $post->slug
                ));
            } catch (\Throwable $e) {
                report($e); 
            }
        }

        return redirect(url()->previous() . '#comment-' . $comment->id)
            ->with('success', $parent ? 'Reply added successfully!' : 'Comment added successfully!');
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment->update([
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Comment updated successfully!');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        
        $comment->delete();

        return back()->with('success', 'Comment deleted successfully!');
    }
}