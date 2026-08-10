<?php

namespace App\Http\Controllers;

use App\Models\Shout;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ShoutController extends Controller
{
    public function store(Request $request, User $user, NotificationService $notifications): RedirectResponse
    {
        $author = $request->user();

        abort_unless($author->canPost(), 403, 'Your account is restricted from posting.');

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:'.Shout::MAX_LENGTH],
        ]);

        $shout = Shout::create([
            'user_id' => $user->id,
            'author_id' => $author->id,
            'body' => trim($validated['body']),
        ]);

        $notifications->recordShout($user, $author);

        return redirect()
            ->to(route('users.show', $user).'#shouts')
            ->with('success', 'Shout posted.');
    }

    public function destroy(Request $request, Shout $shout): RedirectResponse
    {
        abort_unless($shout->canBeDeletedBy($request->user()), 403);

        $profileUser = $shout->user;
        $shout->delete();

        return redirect()
            ->to(route('users.show', $profileUser).'#shouts')
            ->with('success', 'Shout deleted.');
    }
}
