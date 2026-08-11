<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->input('tab') === 'archived' ? 'archived' : 'new';

        $query = ContactMessage::query()
            ->with('user')
            ->where('is_archived', $tab === 'archived')
            ->latest();

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($builder) use ($search) {
                $builder->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(20)->withQueryString();
        $newCount = ContactMessage::where('is_archived', false)->count();
        $archivedCount = ContactMessage::where('is_archived', true)->count();

        return view('admin.messages.index', compact(
            'messages',
            'tab',
            'newCount',
            'archivedCount',
        ));
    }

    public function archive(ContactMessage $message): RedirectResponse
    {
        if (! $message->is_archived) {
            $message->forceFill([
                'is_archived' => true,
                'archived_at' => now(),
            ])->save();
        }

        return redirect()
            ->route('admin.messages.index', request()->only('tab', 'search', 'page'))
            ->with('success', 'Message marked as done.');
    }

    public function unarchive(ContactMessage $message): RedirectResponse
    {
        if ($message->is_archived) {
            $message->forceFill([
                'is_archived' => false,
                'archived_at' => null,
            ])->save();
        }

        return redirect()
            ->route('admin.messages.index', array_merge(
                ['tab' => 'archived'],
                request()->only('search', 'page'),
            ))
            ->with('success', 'Message restored to New.');
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $label = $message->username.' — '.$message->subject;
        $id = $message->id;
        $wasArchived = $message->is_archived;

        $message->delete();

        AdminActivityLog::record(
            auth()->user(),
            AdminActivityLog::ACTION_CONTACT_MESSAGE_DELETED,
            'Deleted contact message from '.$label,
            'contact_message',
            $id,
            $label,
        );

        return redirect()
            ->route('admin.messages.index', array_filter([
                'tab' => $wasArchived ? 'archived' : null,
                'search' => request('search'),
            ]))
            ->with('success', 'Message deleted.');
    }
}
