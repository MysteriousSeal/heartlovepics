<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Image;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, string $slug, NotificationService $notifications): RedirectResponse
    {
        if ($request->user()?->is_banned) {
            abort(403, 'Your account is restricted from commenting.');
        }

        $image = $this->findImageForComment($slug, $request);

        $parentId = $request->input('parent_id');

        if ($parentId) {
            $parent = Comment::query()
                ->where('image_id', $image->id)
                ->whereKey($parentId)
                ->firstOrFail();

            if ($parent->depth() >= Comment::MAX_REPLY_DEPTH) {
                throw ValidationException::withMessages([
                    'body' => 'Replies cannot be nested deeper than '.Comment::MAX_REPLY_DEPTH.' levels.',
                ]);
            }

            $parentId = $parent->id;
        }

        $user = $request->user();
        $authorName = $user
            ? $user->username
            : ($request->input('author_name') ?: 'Anonymous');

        $reply = $image->comments()->create([
            'parent_id' => $parentId,
            'user_id' => $user?->id,
            'author_name' => $authorName,
            'body' => $request->input('body'),
        ]);

        $actor = $user;
        $notifications->recordImageComment($image, $reply, $actor);
        $notifications->recordCommentMentions($image, $reply, $actor, $parentId ? $parent : null);

        if ($parentId) {
            $notifications->recordCommentReply($reply, $parent, $actor);
        }

        return redirect()
            ->route('images.show', $slug)
            ->with('success', 'Comment added.')
            ->withFragment($parentId ? 'comment-'.$parentId : 'comments');
    }

    private function findImageForComment(string $slug, Request $request): Image
    {
        $image = Image::query()->where('slug', $slug)->firstOrFail();

        if (! $image->isVisibleTo($request->user())) {
            abort(404);
        }

        return $image;
    }
}