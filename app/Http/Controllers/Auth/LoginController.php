<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        if (! Auth::attempt(
            [
                'username' => $request->input('username'),
                'password' => $request->input('password'),
                'is_admin' => false,
            ],
            $request->boolean('remember'),
        )) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Invalid credentials.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }
}