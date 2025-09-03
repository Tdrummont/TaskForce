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
        $locale = $request->attributes->get('locale', app()->getLocale() ?: config('app.locale', 'pt'));

        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', ['locale' => $locale], false))
                    : Inertia::render('Auth/VerifyEmail', ['status' => session('status')]);
    }
}
