<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function unreadCount(Request $request, NotificationService $notifications): JsonResponse
    {
        return response()->json([
            'count' => $notifications->unreadCount($request->user()),
        ]);
    }

    public function index(Request $request, NotificationService $notifications): View
    {
        $user = $request->user();

        return view('notifications.index', [
            'groups' => $notifications->groupedPaginatedFor($user),
            'unreadCount' => $notifications->unreadCount($user),
            'user' => $user,
        ]);
    }

    public function visitGroup(Request $request, NotificationService $notifications): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string'],
            // Follow notifications are not tied to an image.
            'image_id' => ['nullable', 'integer'],
            'comment_id' => ['nullable', 'integer'],
        ]);

        $imageId = isset($validated['image_id']) ? (int) $validated['image_id'] : null;

        $notifications->markGroupAsRead(
            $request->user(),
            $validated['type'],
            $imageId,
            isset($validated['comment_id']) ? (int) $validated['comment_id'] : null,
        );

        $notification = UserNotification::query()
            ->where('user_id', $request->user()->id)
            ->where('type', $validated['type'])
            ->when($imageId, fn ($query) => $query->where('image_id', $imageId))
            ->when(
                isset($validated['comment_id']),
                fn ($query) => $query->where('comment_id', $validated['comment_id']),
            )
            ->latest()
            ->first();

        return redirect()->to($notification?->targetUrl() ?? route('home'));
    }

    public function updatePreferences(Request $request, NotificationService $notifications): RedirectResponse
    {
        $validated = $request->validate([
            'notify_likes' => ['sometimes', 'boolean'],
            'notify_comments' => ['sometimes', 'boolean'],
            'notify_bookmarks' => ['sometimes', 'boolean'],
            'notify_replies' => ['sometimes', 'boolean'],
            'notify_mentions' => ['sometimes', 'boolean'],
            'notify_follows' => ['sometimes', 'boolean'],
            'notify_shouts' => ['sometimes', 'boolean'],
        ]);

        $notifications->updatePreferences($request->user(), [
            'notify_likes' => $request->boolean('notify_likes'),
            'notify_comments' => $request->boolean('notify_comments'),
            'notify_bookmarks' => $request->boolean('notify_bookmarks'),
            'notify_replies' => $request->boolean('notify_replies'),
            'notify_mentions' => $request->boolean('notify_mentions'),
            'notify_follows' => $request->boolean('notify_follows'),
            'notify_shouts' => $request->boolean('notify_shouts'),
        ]);

        return redirect()
            ->route('notifications.index')
            ->with('success', 'Notification preferences saved.');
    }

    public function markAllRead(Request $request, NotificationService $notifications): RedirectResponse
    {
        $notifications->markAllAsRead($request->user());

        return redirect()
            ->route('notifications.index')
            ->with('success', 'All notifications marked as read.');
    }
}