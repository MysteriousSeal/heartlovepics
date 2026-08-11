<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()
            ->where('is_admin', false)
            ->withCount([
                // List stats
                'images',
                'imageLikes as likes_given_count',
                'receivedImageLikes as likes_received_count',
                'comments as comments_posted_count',
                // Extra counts for canBeDeleted() / deletionBlockers()
                // (images/comments/imageLikes already counted above)
                'commentLikes',
                'imageBookmarks',
                'collections',
                'following',
                'followers',
                'shouts',
                'shoutsPosted',
                'journals',
                'notifications',
            ]);

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where('username', 'like', "%{$search}%");
        }

        if ($request->input('status') === 'banned') {
            $query->where('is_banned', true);
        } elseif ($request->input('status') === 'active') {
            $query->where('is_banned', false);
        }

        $sort = $request->input('sort');

        match ($sort) {
            'oldest' => $query->oldest(),
            'images' => $query->orderByDesc('images_count'),
            'likes_received' => $query->orderByDesc('likes_received_count'),
            'comments' => $query->orderByDesc('comments_posted_count'),
            default => $query->latest(),
        };

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users', 'sort'));
    }

    public function ban(User $user): RedirectResponse
    {
        abort_if($user->is_admin, 403);

        if ($user->is_banned) {
            return redirect()
                ->route('admin.users.index')
                ->with('success', '@'.$user->username.' is already banned.');
        }

        $user->update(['is_banned' => true]);

        AdminActivityLog::record(
            auth()->user(),
            AdminActivityLog::ACTION_USER_BANNED,
            'Banned @'.$user->username,
            'user',
            $user->id,
            $user->username,
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', '@'.$user->username.' has been banned.');
    }

    public function unban(User $user): RedirectResponse
    {
        abort_if($user->is_admin, 403);

        if (! $user->is_banned) {
            return redirect()
                ->route('admin.users.index')
                ->with('success', '@'.$user->username.' is not banned.');
        }

        $user->update(['is_banned' => false]);

        AdminActivityLog::record(
            auth()->user(),
            AdminActivityLog::ACTION_USER_UNBANNED,
            'Unbanned @'.$user->username,
            'user',
            $user->id,
            $user->username,
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', '@'.$user->username.' has been unbanned.');
    }

    /**
     * Hard-delete an empty account — only when it has no content or engagement.
     */
    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->is_admin, 403);

        // Fresh counts so the gate is not based on a stale list row.
        $user->loadCount([
            'images',
            'comments',
            'imageLikes',
            'commentLikes',
            'imageBookmarks',
            'collections',
            'following',
            'followers',
            'shouts',
            'shoutsPosted',
            'journals',
            'notifications',
        ]);

        if (! $user->canBeDeleted()) {
            $blockers = $user->deletionBlockers();

            return redirect()
                ->route('admin.users.index')
                ->with('error', '@'.$user->username.' cannot be deleted while they still have '
                    .implode(', ', $blockers).'.');
        }

        $username = $user->username;
        $userId = $user->id;
        $avatarPath = $user->avatar_path;
        $bannerPath = $user->banner_path;

        $user->delete();

        if ($avatarPath) {
            Storage::disk('public')->delete($avatarPath);
        }

        if ($bannerPath) {
            Storage::disk('public')->delete($bannerPath);
        }

        AdminActivityLog::record(
            auth()->user(),
            AdminActivityLog::ACTION_USER_DELETED,
            'Deleted empty account @'.$username,
            'user',
            $userId,
            $username,
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', '@'.$username.' has been deleted.');
    }
}