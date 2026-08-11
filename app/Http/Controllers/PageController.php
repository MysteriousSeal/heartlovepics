<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function privacy(): View
    {
        return view('home.privacy');
    }

    public function terms(): View
    {
        return view('home.terms');
    }

    public function changelog(): View
    {
        return view('home.changelog');
    }

    public function contact(): View
    {
        return view('home.contact');
    }

    public function submitContact(Request $request): RedirectResponse
    {
        $loggedIn = $request->user();

        $validated = $request->validate([
            'username' => $loggedIn
                ? ['nullable', 'string']
                : ['required', 'string', 'min:2', 'max:'.ContactMessage::USERNAME_MAX],
            'email' => ['required', 'email:rfc', 'max:'.ContactMessage::EMAIL_MAX],
            'subject' => ['required', 'string', 'max:'.ContactMessage::SUBJECT_MAX],
            'message' => ['required', 'string', 'max:'.ContactMessage::MESSAGE_MAX],
        ]);

        $username = $loggedIn
            ? $loggedIn->username
            : trim($validated['username']);

        ContactMessage::create([
            'user_id' => $loggedIn?->id,
            'username' => $username,
            'email' => strtolower(trim($validated['email'])),
            'subject' => trim($validated['subject']),
            'message' => trim($validated['message']),
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('pages.contact')
            ->with('success', 'Thanks — your message has been sent.');
    }
}