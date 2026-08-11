<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContactReplyMail;
use App\Models\AdminActivityLog;
use App\Models\ContactMessage;
use App\Models\ContactMessageReply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->input('tab') === 'archived' ? 'archived' : 'new';

        $query = ContactMessage::query()
            ->with(['user', 'replies.admin'])
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

    public function reply(Request $request, ContactMessage $message): RedirectResponse
    {
        $validated = $request->validate([
            'reply_subject' => ['required', 'string', 'max:'.ContactMessage::REPLY_SUBJECT_MAX],
            'reply_body' => ['required', 'string', 'max:'.ContactMessage::REPLY_BODY_MAX],
            'archive_after' => ['sometimes', 'boolean'],
        ]);

        $replySubject = trim($validated['reply_subject']);
        $replyBody = trim($validated['reply_body']);
        $archiveAfter = $request->boolean('archive_after');

        try {
            Mail::to($message->email, $message->username)
                ->send(new ContactReplyMail($message, $replySubject, $replyBody));
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('admin.messages.index', array_filter([
                    'tab' => $message->is_archived ? 'archived' : 'new',
                    'search' => $request->input('search'),
                    'reply' => $message->id,
                ]))
                ->withInput()
                ->with('error', 'Could not send the email. Check mail configuration and try again.');
        }

        ContactMessageReply::create([
            'contact_message_id' => $message->id,
            'admin_id' => auth()->id(),
            'subject' => $replySubject,
            'body' => $replyBody,
            'from_address' => (string) config('mail.from.address'),
        ]);

        $updates = [
            'replied_at' => now(),
            // Keep last reply mirrored for quick listing / legacy fields.
            'reply_subject' => $replySubject,
            'reply_body' => $replyBody,
        ];

        if ($archiveAfter && ! $message->is_archived) {
            $updates['is_archived'] = true;
            $updates['archived_at'] = now();
        }

        $message->forceFill($updates)->save();

        AdminActivityLog::record(
            auth()->user(),
            AdminActivityLog::ACTION_CONTACT_MESSAGE_REPLIED,
            'Replied to contact message from '.$message->username.' ('.$message->email.')',
            'contact_message',
            $message->id,
            $message->username.' — '.$message->subject,
        );

        $success = 'Reply sent to '.$message->email.' from '.config('mail.from.address').'.';
        if ($archiveAfter) {
            $success .= ' Message marked as done.';
        }

        return redirect()
            ->route('admin.messages.index', array_filter([
                'tab' => $archiveAfter || $message->fresh()->is_archived ? 'archived' : 'new',
                'search' => $request->input('search'),
            ]))
            ->with('success', $success);
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
