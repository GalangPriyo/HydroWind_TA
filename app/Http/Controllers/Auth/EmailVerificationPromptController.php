<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|Response
    {
        if ($request->user()->hasVerifiedEmail()) {
            // Redirect berdasarkan peran user
            return match ($request->user()->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'user' => redirect()->route('user.dashboard'),
            };
        }

        return Inertia::render('Auth/VerifyEmail', ['status' => session('status')]);
    }
}
