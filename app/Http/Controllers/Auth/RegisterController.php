<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Support\UsernameGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function suggestUsername(): JsonResponse
    {
        return response()->json(['username' => UsernameGenerator::generate()]);
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $username = $request->input('username');

        $user = User::create([
            'name' => $username,
            'username' => $username,
            'email' => null,
            'password' => $request->input('password'),
            'is_admin' => false,
            'terms_accepted_at' => now(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'Welcome to HeartLovePics!');
    }
}