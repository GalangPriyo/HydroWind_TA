<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectToDashboard($request);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->redirectToDashboard($request);
    }

    /**
     * Redirect user to their respective dashboard based on role.
     */
    protected function redirectToDashboard(EmailVerificationRequest $request): RedirectResponse
    {
        return match ($request->user()->role) {
            'admin' => redirect()->intended(route('admin.dashboard') . '?verified=1'),
            'user' => redirect()->intended(route('user.dashboard') . '?verified=1'),
        };
    }
}
