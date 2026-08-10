<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Comment::query()
            ->with(['image', 'user', 'parent'])
            ->withCount('likes')
            ->latest();

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($builder) use ($search) {
                $builder->where('author_name', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%")
                    ->orWhereHas('image', fn ($imageQuery) => $imageQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%"));
            });
        }

        if ($request->input('type') === 'replies') {
            $query->whereNotNull('parent_id');
        } elseif ($request->input('type') === 'top') {
            $query->whereNull('parent_id');
        }

        $comments = $query->paginate(20)->withQueryString();

        return view('admin.comments.index', compact('comments'));
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->loadMissing('image');
        $authorName = $comment->author_name;
        $imageTitle = $comment->image?->title ?? 'a deleted image';
        $commentId = $comment->id;

        $comment->delete();

        AdminActivityLog::record(
            auth()->user(),
            AdminActivityLog::ACTION_COMMENT_DELETED,
            'Deleted comment by '.$authorName.' on "'.$imageTitle.'"',
            'comment',
            $commentId,
            $authorName,
        );

        return redirect()
            ->route('admin.comments.index')
            ->with('success', 'Comment deleted.');
    }
}