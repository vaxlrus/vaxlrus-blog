<?php

namespace App\Http\Controllers;

use App\Services\UserDeletingService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show() {
        return view('profile.index', [
            'user' => auth()->user(),
            'profileRecoverPeriod' => date("d.m.Y", strtotime("+".UserDeletingService::PROFILE_RESTORATION_DAYS." days"))
        ]);
    }

    public function deleteAccount(UserDeletingService $userDeletingService) {
        $user = auth()->user();

        $userDeletingService->delete($user);

        auth()->logout();

        return redirect('/');
    }
}
