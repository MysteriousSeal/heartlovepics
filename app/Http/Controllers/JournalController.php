<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Support\HtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JournalController extends Controller
{
    public function show(Journal $journal): View
    {
        $journal->load('user');
        $canManage = $journal->canBeManagedBy(auth()->user());

        return view('journals.show', compact('journal', 'canManage'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()->is_banned) {
            return redirect()->route('home');
        }

        return view('journals.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if($request->user()->is_banned, 403, 'Your account is restricted from posting.');

        $validated = $this->validated($request);

        $journal = Journal::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'body' => HtmlSanitizer::clean($validated['body']),
        ]);

        return redirect()
            ->route('journals.show', $journal)
            ->with('success', 'Journal posted.');
    }

    public function edit(Journal $journal): View
    {
        $this->authorizeOwner($journal);

        return view('journals.edit', compact('journal'));
    }

    public function update(Request $request, Journal $journal): RedirectResponse
    {
        $this->authorizeOwner($journal);

        $validated = $this->validated($request);

        $journal->update([
            'title' => $validated['title'],
            'body' => HtmlSanitizer::clean($validated['body']),
        ]);

        return redirect()
            ->route('journals.show', $journal)
            ->with('success', 'Journal updated.');
    }

    public function destroy(Journal $journal): RedirectResponse
    {
        $this->authorizeOwner($journal);
        $user = $journal->user;

        $journal->delete();

        return redirect()
            ->route('users.journal', $user)
            ->with('success', 'Journal deleted.');
    }

    /** @return array{title: string, body: string} */
    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:'.Journal::TITLE_MAX_LENGTH],
            'body' => ['required', 'string', 'max:'.Journal::BODY_MAX_LENGTH],
        ]);
    }

    private function authorizeOwner(Journal $journal): void
    {
        abort_unless($journal->canBeManagedBy(auth()->user()), 403);
    }
}
