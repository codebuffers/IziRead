<?php

namespace App\Http\Controllers\Auth;

use App\Enums\CustomerStatus;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('home').'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            $customer = $request->user()->customer;
            $customer->status = CustomerStatus::Active->value;
            $customer->save();
            event(new Verified($request->user()));
        }

        // Redirect based on user role
        if ($request->user()->is_seller()) {
            return redirect()->intended(route('app.dashboard').'?verified=1');
        } else {
            return redirect()->intended(route('home').'?verified=1');
        }
    }
}
