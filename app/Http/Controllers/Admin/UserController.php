<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()
            ->where('is_admin', false)
            ->withCount([
                'images',
                'imageLikes as likes_given_count',
                'receivedImageLikes as likes_received_count',
                'comments as comments_posted_count',
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
}