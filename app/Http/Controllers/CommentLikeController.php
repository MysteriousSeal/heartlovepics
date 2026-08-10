<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Services\CommentLikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentLikeController extends Controller
{
    public function toggle(Request $request, Comment $comment, CommentLikeService $likes): JsonResponse
    {
        $comment->loadMissing('image');

        if (! $comment->image || ! $comment->image->isVisibleTo($request->user())) {
            abort(404);
        }

        return response()->json($likes->toggle($comment, $request));
    }
}