<?php

namespace App\Http\Controllers\Auth;

use App\Models\Utility;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\clearLoginAttempts;
use Illuminate\Validation\ValidationException;
use App\Models\LoginDetail;
use App\Models\Client;
use App\Models\UserWorkspace;
use App\Models\ClientWorkspace;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     *
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        if (!file_exists(storage_path() . "/installed")) {
            header('location:install');
            die;
        }
        //$this->middleware('guest')->except('logout');
        // $this->middleware('guest:client')->except(['logout']);
    }

    public function create()
    {
        // return view('auth.login');
    }

    public function store(LoginRequest $request)
    {
        $checkUser = User::where('email', '=', $request->email)->where('type', '!=', 'admin')->first();
        if ($checkUser) {
            if ($checkUser->is_enable_login == 1) {
                $currentWorkspace = UserWorkspace::where('workspace_id', '=', $checkUser->currant_workspace)->where('user_id', $checkUser->id)->first();
                if ($currentWorkspace->is_active == 0) {
                    $userOtherWorkspace = UserWorkspace::where('user_id', $checkUser->id)->where('is_active', 1)->get();
                    if (count($userOtherWorkspace)) {
                        $userOtherWorkspace = UserWorkspace::where('user_id', $checkUser->id)->where('is_active', 1)->first();
                        $checkUser->currant_workspace = $userOtherWorkspace->workspace_id;
                        $checkUser->save();
                    } else {
                        return redirect()->back()->with('error', 'Your Account is disable, please contact your Administrator.');
                    }
                }
            } else {
                return redirect()->back()->with('error', 'Your Account is disable, please contact your Administrator.');
            }
        }
        $setting = Utility::getAdminPaymentSettings();
        if ($setting['recaptcha_module'] == 'on') {
            $validation['g-recaptcha-response'] = 'required';
        } else {
            $validation = [];
        }
        $this->validate($request, $validation);
        $request->authenticate();
        $request->session()->regenerate();

        $ip = $_SERVER['REMOTE_ADDR'];
        if (!empty($ip)) {

            $ip = '49.36.83.154'; // This is static ip address
        }
        $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));
        $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
        if ($whichbrowser->device->type == 'bot') {
            return;
        }
        $referrer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : null;
        /* Detect extra details about the user */
        $query['browser_name'] = $whichbrowser->browser->name ?? null;
        $query['os_name'] = $whichbrowser->os->name ?? null;
        $query['browser_language'] = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
        $query['device_type'] = self::get_device_type($_SERVER['HTTP_USER_AGENT']);
        $query['referrer_host'] = !empty($referrer['host']);
        $query['referrer_path'] = !empty($referrer['path']);
        // date_default_timezone_set($query['timezone']);

        if ((isset($query['timezone'])) && ($query['timezone'] == 'Europe/Kyiv')) {
            isset($query['timezone']) ? date_default_timezone_set('Europe/Kiev') : '';
        } else {
            isset($query['timezone']) ? date_default_timezone_set($query['timezone']) : '';
        }

        $json = json_encode($query);
        $user = \Auth::user();
        $totalWS = $user->countWorkspace();
        $permission = UserWorkspace::where('user_id', '=', $user->id)->where('permission', '=', 'Member')->count();

        if ($user->type != 'company' && $user->type != 'admin' && ($totalWS <= 0 || $permission > 0)) {
            $login_detail = LoginDetail::create([
                'user_id' => $user->id,
                'ip' => $ip,
                'date' => date('Y-m-d H:i:s'),
                'details' => $json,
                'type' => 'user',
                'created_by' => $user->currant_workspace,
            ]);
        }
        return redirect()->intended(RouteServiceProvider::HOME);
    }



    public function showVerifcation(Request $request, $lang = '')
    {
        $adminDefaultLang = Utility::getAdminPaymentSettings();
        if ($lang == '') {
            $lang = $adminDefaultLang['default_lang'] ?? 'en';
        }
        \App::setLocale($lang);
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(RouteServiceProvider::HOME)
            : view('auth.verify-email', ['statuss' => session('statuss'), 'lang' => $lang]);
    }


    public function showClientLoginForm($lang = '')
    {

        if ($lang == '') {
            // $lang = env('DEFAULT_LANG') ?? 'en';
            $adminDefaultLang = Utility::getAdminPaymentSettings();
            $lang = $adminDefaultLang['default_lang'];
        }

        $langList = Utility::langList();
        $lang = array_key_exists($lang, $langList) ? $lang : 'en';
        if (empty($lang)) {
            $lang = Utility::getValByName('default_language');
        }
        \App::setLocale($lang);

        return view('auth.client_login', compact('lang'));
    }

    public function clientLogin(Request $request)
    {
        $checkClient = Client::where('email', '=', $request->email)->first();
        if ($checkClient) {
            if ($checkClient->is_enable_login == 1) {
                $currentWorkspace = ClientWorkspace::where('workspace_id', '=', $checkClient->currant_workspace)->where('client_id', $checkClient->id)->first();
                if ($currentWorkspace->is_active == 0) {
                    $clientOtherWorkspace = ClientWorkspace::where('client_id', $checkClient->id)->where('is_active', 1)->get();

                    if (count($clientOtherWorkspace)) {
                        $userOtherWorkspace = ClientWorkspace::where('client_id', $checkClient->id)->where('is_active', 1)->first();
                        $checkClient->currant_workspace = $userOtherWorkspace->workspace_id;
                        $checkClient->save();
                    } else {
                        return redirect()->back()->with('error', 'Your Account is disable, please contact your Administrator.');
                    }
                }
            } else {
                return redirect()->back()->with('error', 'Your Account is disable, please contact your Administrator.');
            }
        }
        $setting = Utility::getAdminPaymentSettings();
        if ($setting['recaptcha_module'] == 'on') {
            $validation['g-recaptcha-response'] = 'required';
        } else {
            $validation = [];
        }
        $this->validate($request, $validation);
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:4'
        ]);

        if (\Auth::guard('client')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {

            $ip = $_SERVER['REMOTE_ADDR'];
            if (!empty($ip)) {

                $ip = '49.36.83.154'; // This is static ip address
            }
            $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));
            $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
            if ($whichbrowser->device->type == 'bot') {
                return;
            }
            $referrer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : null;
            /* Detect extra details about the user */
            $query['browser_name'] = $whichbrowser->browser->name ?? null;
            $query['os_name'] = $whichbrowser->os->name ?? null;
            $query['browser_language'] = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
            $query['device_type'] = self::get_device_type($_SERVER['HTTP_USER_AGENT']);
            $query['referrer_host'] = !empty($referrer['host']);
            $query['referrer_path'] = !empty($referrer['path']);
            date_default_timezone_set($query['timezone']);
            $json = json_encode($query);

            $client = Client::where('email', '=', $request->email)->first();
            // $user = \Auth::user();
            $login_detail = LoginDetail::create([
                'user_id' => $client->id,
                'ip' => $ip,
                'date' => date('Y-m-d H:i:s'),
                'details' => $json,
                'type' => 'client',
                'created_by' => $client->currant_workspace,
            ]);
            return redirect()->route('client.home');
        }
        return $this->sendFailedLoginResponse($request);
    }

    public function showLoginForm($lang = '')
    {
        if ($lang == '') {
            // $lang = env('DEFAULT_LANG') ?? 'en';
            $adminDefaultLang = Utility::getAdminPaymentSettings();
            $lang = $adminDefaultLang['default_lang'];
        }

        $langList = Utility::langList();
        $lang = array_key_exists($lang, $langList) ? $lang : 'en';
        if (empty($lang)) {
            $lang = Utility::getValByName('default_language');
        }
        \App::setLocale($lang);

        return view('auth.login', compact('lang'));
    }




    public function showLinkRequestForm($lang = '')
    {
        if ($lang == '') {
            $adminDefaultLang = Utility::getAdminPaymentSettings();
            $lang = $adminDefaultLang['default_lang'] ?? 'en';
        }
        \App::setLocale($lang);
        return view('auth.forgot-password', compact('lang'));
    }




    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }
    public function username()
    {
        return 'email';
    }

    function get_device_type($user_agent)
    {
        $mobile_regex = '/(?:phone|windows\s+phone|ipod|blackberry|(?:android|bb\d+|meego|silk|googlebot) .+? mobile|palm|windows\s+ce|opera mini|avantgo|mobilesafari|docomo)/i';
        $tablet_regex = '/(?:ipad|playbook|(?:android|bb\d+|meego|silk)(?! .+? mobile))/i';
        if (preg_match_all($mobile_regex, $user_agent)) {
            return 'mobile';
        } else {
            if (preg_match_all($tablet_regex, $user_agent)) {
                return 'tablet';
            } else {
                return 'desktop';
            }
        }
    }
}
