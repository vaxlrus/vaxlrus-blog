<?php

namespace App\Http\Controllers;

use App\Services\UserDeletingService;
use App\Services\UserRestorationService;
use Illuminate\Support\Carbon;


class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.index', [
            'user' => auth()->user(),
            'profileRecoverPeriod' => Carbon::now()->addDays(UserRestorationService::PROFILE_RESTORATION_DAYS)->format('d.m.Y')
        ]);
    }

    public function deleteAccount(UserDeletingService $userDeletingService)
    {
        $user = auth()->user();

        $userDeletingService->delete($user);

        auth()->logout();

        return redirect('/');
    }
}
