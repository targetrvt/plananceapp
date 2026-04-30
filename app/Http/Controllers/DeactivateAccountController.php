<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DeactivateAccountController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $deactivatedAt = now()->timestamp;
        $fallbackDomain = 'deactivated.local';

        $user->forceFill([
            'name' => $user->name . ' (Deactivated)',
            'email' => "deactivated-{$user->id}-{$deactivatedAt}@{$fallbackDomain}",
            'email_verified_at' => null,
            'password' => Hash::make(Str::random(64)),
            'remember_token' => null,
        ])->save();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'account-deactivated');
    }
}
