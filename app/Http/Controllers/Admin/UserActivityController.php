<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Image;
use App\Models\ImageBookmark;
use App\Models\ImageLike;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class UserActivityController extends Controller
{
    private const PER_PAGE = 40;

    private const FETCH_PER_TYPE = 200;

    private const TYPES = [
        'signup' => 'New account',
        'like' => 'Liked a post',
        'like_cron' => 'Cron-seeded like',
        'comment' => 'New comment',
        'bookmark' => 'Bookmarked a post',
    ];

    public function index(Request $request): View
    {
        $type = $request->input('type');
        $hideCron = $request->boolean('hide_cron');

        $allEvents = collect()
            ->concat($this->signups())
            ->concat($this->likes())
            ->concat($this->comments())
            ->concat($this->bookmarks());

        $typeCounts = $allEvents->countBy('type');

        $events = $allEvents
            ->when($hideCron, fn ($events) => $events->reject(fn ($event) => $event['type'] === 'like_cron'))
            ->when($type && array_key_exists($type, self::TYPES), fn ($events) => $events->where('type', $type))
            ->sortByDesc('created_at')
            ->values();

        $types = self::TYPES;

        $page = (int) $request->input('page', 1);
        $slice = $events->slice(($page - 1) * self::PER_PAGE, self::PER_PAGE)->values();

        $activities = new LengthAwarePaginator(
            $slice,
            $events->count(),
            self::PER_PAGE,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        return view('admin.user-activity.index', compact('activities', 'types', 'type', 'hideCron', 'typeCounts'));
    }

    private function signups()
    {
        return User::query()
            ->latest('created_at')
            ->limit(self::FETCH_PER_TYPE)
            ->get(['id', 'username', 'avatar_path', 'created_at'])
            ->map(fn (User $user) => [
                'type' => 'signup',
                'created_at' => $user->created_at,
                'user' => $user,
                'guest_label' => null,
                'image' => null,
                'comment_body' => null,
            ]);
    }

    private function likes()
    {
        return ImageLike::query()
            ->with(['user:id,username,avatar_path', 'image:id,title,slug'])
            ->latest('created_at')
            ->limit(self::FETCH_PER_TYPE)
            ->get()
            ->map(function (ImageLike $like) {
                $isCron = str_starts_with((string) $like->fingerprint, 'cron-');

                return [
                    'type' => $isCron ? 'like_cron' : 'like',
                    'created_at' => $like->created_at,
                    'user' => $like->user,
                    'guest_label' => $like->user ? null : ($isCron ? 'CRON Bot' : 'Guest'),
                    'image' => $like->image,
                    'comment_body' => null,
                ];
            });
    }

    private function bookmarks()
    {
        return ImageBookmark::query()
            ->with(['user:id,username,avatar_path', 'image:id,title,slug'])
            ->latest('created_at')
            ->limit(self::FETCH_PER_TYPE)
            ->get()
            ->map(fn (ImageBookmark $bookmark) => [
                'type' => 'bookmark',
                'created_at' => $bookmark->created_at,
                'user' => $bookmark->user,
                'guest_label' => null,
                'image' => $bookmark->image,
                'comment_body' => null,
            ]);
    }

    private function comments()
    {
        return Comment::query()
            ->with(['user:id,username,avatar_path', 'image:id,title,slug'])
            ->latest('created_at')
            ->limit(self::FETCH_PER_TYPE)
            ->get(['id', 'user_id', 'author_name', 'image_id', 'body', 'created_at'])
            ->map(fn (Comment $comment) => [
                'type' => 'comment',
                'created_at' => $comment->created_at,
                'user' => $comment->user,
                'guest_label' => $comment->user ? null : ($comment->author_name ?: 'Guest'),
                'image' => $comment->image,
                'comment_body' => \Illuminate\Support\Str::limit(strip_tags($comment->body), 120),
            ]);
    }
}
