<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\Utility;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        // $request->user()->sendEmailVerificationNotification();
        // Utility::setMailConfig();
        Utility::sendEmailSetup();
        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Something Went Wrong...'));
        }
        return back()->with('statuss', 'verification-link-sent');
    }
}
