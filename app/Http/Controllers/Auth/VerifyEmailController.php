<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return match ($request->user()->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'user' => redirect()->route('user.dashboard'),
            };
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return match ($request->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'user' => redirect()->route('user.dashboard'),
        };
    }
}
