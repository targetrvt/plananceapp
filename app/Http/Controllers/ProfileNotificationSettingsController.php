<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileNotificationSettingsController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'notify_budget_warnings' => ['required', 'in:on,off'],
            'notify_budget_limit_email' => ['required', 'in:on,off'],
        ]);

        $request->user()->forceFill([
            'notify_budget_warnings' => $data['notify_budget_warnings'] === 'on',
            'notify_budget_limit_email' => $data['notify_budget_limit_email'] === 'on',
        ])->save();

        return back()->with('status', 'email-notifications-updated');
    }
}
