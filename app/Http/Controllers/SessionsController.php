<?php

namespace App\Http\Controllers;

use App\Services\UserDeletingService;
use App\Services\UserRestorationService;
use Illuminate\Validation\ValidationException;

class SessionsController extends Controller
{
    public function create()
    {
        return view('sessions.create');
    }

    public function store(UserDeletingService $userDeletingService, UserRestorationService $userRestorationService)
    {
        $attributes = request()->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (! $userDeletingService->isAccountRestorable($attributes['email'])) {
            throw ValidationException::withMessages([
//                'email' => 'Your provided credentials could not be verified.'
                'email' => 'Аккаунт не возможно восстановить'
            ]);
        }
        $userRestorationService->restoreAccount($attributes['email']);

        if (! auth()->attempt($attributes)) {
            throw ValidationException::withMessages([
                'email' => 'Your provided credentials could not be verified.'
            ]);
        }

        session()->regenerate();

        return redirect('/')->with('success', 'Welcome Back!');
    }

    public function destroy()
    {
        auth()->logout();

        return redirect('/')->with('success', 'Goodbye!');
    }
}
