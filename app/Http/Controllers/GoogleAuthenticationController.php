<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserWorkspace;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GoogleAuthenticationController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function generate2faSecret(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $hasPermission = Auth::guard('web')->check() && (Auth::user()->type == 'admin' ||
                UserWorkspace::where('permission', 'Owner')->where('user_id', Auth::user()->id)->first());
            $google2fa = new \PragmaRX\Google2FAQRCode\Google2FA();

            if (!$user->google2fa_secret) {
                $user->google2fa_secret = $google2fa->generateSecretKey();
                $user->google2fa_enable = 0;
                $user->save();
            }
            $google2fa = new \PragmaRX\Google2FAQRCode\Google2FA();
            $google2fa_url = $google2fa->getQRCodeInline(
                config('app.name'),
                $user->email,
                $user->google2fa_secret
            );
            $secret_key = $user->google2fa_secret;

            $data = [
                'user'          => $user ?? '',
                'secret'        => $secret_key,
                'google2fa_url' => $google2fa_url,
                'hasPermission' => $hasPermission
            ];
            
            if ($user->type == 'admin') {
                $currentWorkspace = '';
            } else {
                $currentWorkspace = Utility::getWorkspaceBySlug('');
            }
            
            return redirect()->route('users.my.account')->with('success', __('Secret key is generated.'));
        }
        return redirect()->route('login')->with('error', __('Please log in to generate a 2FA secret.'));
    }

    /**
     * Enable 2FA
     */
    public function enable2fa(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());

        $secret = $request->input('secret');
        $valid = $google2fa->verifyKey($user->google2fa_secret, $secret);
        if ($valid) {
            $user->google2fa_enable = 1;
            $user->save();
            return redirect()->route('users.my.account')->with('success', __('2FA is enabled successfully.'));
        } else {
            return redirect()->route('users.my.account')->with('error', __('Invalid verification Code, Please try again.'));
        }
    }

    /**
     * Disable 2FA
     */
    public function disable2fa(Request $request)
    {
        $validatedData = $request->validate([
            'current-password' => 'required',
        ]);

        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            return redirect()->route('users.my.account')->with('error', __('Your password does not matches with your account password.'));
        }

        $user = User::find(Auth::user()->id);

        $user->google2fa_enable = 0;
        $user->google2fa_secret = null;

        $user->save();
        return redirect()->route('users.my.account')->with('success', __('2FA is disabled.'));
    }
}
