<?php

namespace App\Http\Controllers;

use App\Models\Mail\EmailTest;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\Utility;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function index()
    {
        $user = \Auth::user();
        if ($user->type == 'admin') {
            $workspace = new Workspace();
            // $settings = Utility::getcompanySettings($currentWorkspace->id);
            return view('setting', compact('workspace'));
        } else {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }

    public function store(Request $request)
    {

        $user = Auth::user();
        if ($user->type == 'admin') {
            $dir = 'logo/';
            if ($request->favicon) {
                $logo_favicon_Name = 'favicon.png';
                // $request->validate(['favicon' => 'required|image|mimes:png|max:204800']);
                $validator = \Validator::make($request->all(), [
                    'favicon' => 'required|image|mimes:png,jpg,jpeg',

                ]);
                if ($validator->fails()) {

                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', "logo image must have  png file.");
                }

                $path = Utility::upload_file($request, 'favicon', $logo_favicon_Name, $dir, []);
                if ($path['flag'] == 1) {
                    $favicon = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }

                // $request->favicon->storeAs('logo', 'favicon.png');
            }
            if ($request->logo_blue) {
                $logo_dark_Name = 'logo-light.png';
                // $request->validate(['logo_blue' => 'required|image|mimes:png|max:204800']);


                $validator = \Validator::make($request->all(), [
                    'logo_blue' => 'required|image|mimes:png,jpg,jpeg',

                ]);
                if ($validator->fails()) {

                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', "logo image must have  png file.");
                }

                $path = Utility::upload_file($request, 'logo_blue', $logo_dark_Name, $dir, []);
                if ($path['flag'] == 1) {
                    $logo_blue = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }
            if ($request->logo_white) {
                $logo_white_Name = 'logo-dark.png';
                // $request->validate(['logo_white' => 'required|image|mimes:png|max:204800']);

                $validator = \Validator::make($request->all(), [
                    'logo_white' => 'required|image|mimes:png,jpg,jpeg',

                ]);
                if ($validator->fails()) {

                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', "logo image must have  png file.");
                }


                $path = Utility::upload_file($request, 'logo_white', $logo_white_Name, $dir, []);
                if ($path['flag'] == 1) {
                    $logo_white = $path['url'];
                } else {
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }

            $rules = [
                'app_name' => 'required|string|max:50',
                'default_language' => 'required|string|max:50',
                'footer_text' => 'required|string|max:50',
            ];

            $request->validate($rules);
            $cookie_text = (!isset($request->cookie_text) && empty($request->cookie_text)) ? '' : $request->cookie_text;


            // $arrEnv = [

            //     // 'APP_NAME' => $request->app_name,
            //     //'DEFAULT_LANG' => $request->default_language,
            //     // 'FOOTER_TEXT' => $request->footer_text,
            //     // 'DISPLAY_LANDING' => $request->display_landing ? 'on' : 'off',
            //     // 'SITE_RTL' => !isset($request->SITE_RTL) ? 'off' : 'on',
            //     // 'SIGNUP_BUTTON' => !isset($request->SIGNUP_BUTTON) ? 'off' : 'on',

            // ];
            // Utility::setEnvironmentValue($arrEnv);
            // Artisan::call('config:cache');
            // Artisan::call('config:clear');

            $post['app_name'] = $request->app_name;
            $post['footer_text'] = $request->footer_text;
            $post['default_lang'] = $request->default_language;
            $post['display_landing'] = $request->display_landing ? 'on' : 'off';
            $post['site_rtl'] = $request->SITE_RTL ?? 'off';
            $post['signup_button'] = !isset($request->SIGNUP_BUTTON) ? 'off' : 'on';
            $color = (!empty($request->color)) ? $request->color : 'theme-3';
            $post['color'] = $color;
            if (isset($request->color) && $request->color_flag == 'false') {
                $post['color'] = $request->color;
                $post['color_flag'] = $request->color_flag;
            } else {
                $post['color'] = $request->custom_color;
                $post['color_flag'] = $request->color_flag;
            }
            $cust_theme_bg = (!empty($request->cust_theme_bg)) ? 'on' : 'off';
            $post['cust_theme_bg'] = $cust_theme_bg;
            $cust_darklayout = !empty($request->cust_darklayout) ? 'on' : 'off';
            $post['cust_darklayout'] = $cust_darklayout;

            $email_verification = !empty($request->email_verification) ? 'on' : 'off';
            $post['email_verification'] = $email_verification;
            if (isset($post) && !empty($post) && count($post) > 0) {
                $created_at = date('Y-m-d H:i:s');
                $updated_at = date('Y-m-d H:i:s');
                $created_by = 2;
                foreach ($post as $key => $data) {
                    \DB::insert('insert into settings (`value`, `name`,`created_at`,`updated_at`) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`)', [
                        $data,
                        $key,
                        $created_at,
                        $updated_at,
                    ]);
                }
            }

            return redirect()->back()->with('success', __('Setting updated successfully'));
            // if ($this->setEnvironmentValue($arrEnv)) {
            //     return redirect()->back()->with('success', __('Setting updated successfully'));
            // } else {
            //     return redirect()->back()->with('error', __('Something is wrong'));
            // }
        } else {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }

    public function seosetting(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'meta_keywords' => 'required',
                'meta_description' => 'required',
                'meta_image' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        if ($request->meta_image) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'meta_image' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $img_name = time() . '_' . 'meta_image.png';
            $dir = 'uploads/logo/';
            $validation = [
                'max:' . '20480',
            ];
            $path = Utility::upload_file($request, 'meta_image', $img_name, $dir, $validation);

            if ($path['flag'] == 1) {
                $logo_dark = $path['url'];
            } else {
                return redirect()->back()->with('error', __($path['msg']));
            }
            $post['meta_image'] = $img_name;
        }
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        $post['meta_keywords'] = $request->meta_keywords;
        $post['meta_description'] = $request->meta_description;

        foreach ($post as $key => $data) {
            \DB::insert('insert into settings (`value`, `name`,`created_at`,`updated_at`) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`)', [
                $data,
                $key,
                $created_at,
                $updated_at,
            ]);
        }
        return redirect()->back()->with('success', 'Storage setting successfully updated.');
    }

    public function saveCookieSettings(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'cookie_title' => 'required',
                'cookie_description' => 'required',
                'strictly_cookie_title' => 'required',
                'strictly_cookie_description' => 'required',
                'more_information_description' => 'required',
                'contactus_url' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }
        $post = $request->all();

        unset($post['_token']);

        if ($request->enable_cookie) {
            $post['enable_cookie'] = 'on';
        } else {
            $post['enable_cookie'] = 'off';
        }

        if ($request->cookie_logging) {
            $post['cookie_logging'] = 'on';
        } else {
            $post['cookie_logging'] = 'off';
        }

        if ($request->cookie_logging) {
            $post['necessary_cookies'] = 'on';
        } else {
            $post['necessary_cookies'] = 'off';
        }

        if ($request->cookie_title) {
            $post['cookie_title'] = $request->cookie_title;
        }
        if ($request->cookie_description) {
            $post['cookie_description'] = $request->cookie_description;
        }

        if ($request->strictly_cookie_title) {
            $post['strictly_cookie_title'] = $request->strictly_cookie_title;
        }

        if ($request->strictly_cookie_description) {
            $post['strictly_cookie_description'] = $request->strictly_cookie_description;
        }

        if ($request->more_information_description) {
            $post['more_information_description'] = $request->more_information_description;
        }

        if ($request->contactus_url) {
            $post['contactus_url'] = $request->contactus_url;
        }

        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        foreach ($post as $key => $data) {
            \DB::insert('insert into settings (`value`, `name`,`created_at`,`updated_at`) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)', [
                $data,
                $key,
                $created_at,
                $updated_at,
            ]);
        }
        return redirect()->back()->with('success', 'Cookie setting successfully saved.');
    }



    public function CookieConsent(Request $request)
    {

        $settings = Utility::getAdminPaymentSettings();

        if ($settings['enable_cookie'] == "on" && $settings['cookie_logging'] == "on") {
            $allowed_levels = ['necessary', 'analytics', 'targeting'];
            $levels = array_filter($request['cookie'], function ($level) use ($allowed_levels) {
                return in_array($level, $allowed_levels);
            });
            $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
            // Generate new CSV line
            $browser_name = $whichbrowser->browser->name ?? null;
            $os_name = $whichbrowser->os->name ?? null;
            $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
            $device_type = Utility::get_device_type($_SERVER['HTTP_USER_AGENT']);

            $ip = $_SERVER['REMOTE_ADDR'];
            $ip = '49.36.83.154';
            $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));


            $date = (new \DateTime())->format('Y-m-d');
            $time = (new \DateTime())->format('H:i:s') . ' UTC';


            $new_line = implode(',', [
                $ip,
                $date,
                $time,
                json_encode($request['cookie']),
                $device_type,
                $browser_language,
                $browser_name,
                $os_name,
                isset($query) ? $query['country'] : '',
                isset($query) ? $query['region'] : '',
                isset($query) ? $query['regionName'] : '',
                isset($query) ? $query['city'] : '',
                isset($query) ? $query['zip'] : '',
                isset($query) ? $query['lat'] : '',
                isset($query) ? $query['lon'] : ''
            ]);

            if (!file_exists(storage_path() . '/uploads/sample/data.csv')) {

                $first_line = 'IP,Date,Time,Accepted cookies,Device type,Browser language,Browser name,OS Name,Country,Region,RegionName,City,Zipcode,Lat,Lon';
                file_put_contents(storage_path() . '/uploads/sample/data.csv', $first_line . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
            file_put_contents(storage_path() . '/uploads/sample/data.csv', $new_line . PHP_EOL, FILE_APPEND | LOCK_EX);

            return response()->json('success');
        }
        return response()->json('error');
    }
    public function emailSettingStore(Request $request)
    {
        $user = \Auth::user();
        if ($user->type == 'admin') {
            $rules = [
                'mail_driver' => 'required|string|max:50',
                'mail_host' => 'required|string|max:50',
                'mail_port' => 'required|string|max:50',
                'mail_username' => 'required|string|max:50',
                'mail_password' => 'required|string|max:255',
                'mail_encryption' => 'required|string|max:50',
                'mail_from_address' => 'required|string|max:50',
                'mail_from_name' => 'required|string|max:50',
            ];

            $request->validate($rules);

            // $arrEnv = [

            //     'MAIL_DRIVER' => $request->mail_driver,
            //     'MAIL_HOST' => $request->mail_host,
            //     'MAIL_PORT' => $request->mail_port,
            //     'MAIL_USERNAME' => $request->mail_username,
            //     'MAIL_PASSWORD' => $request->mail_password,
            //     'MAIL_ENCRYPTION' => $request->mail_encryption,
            //     'MAIL_FROM_ADDRESS' => $request->mail_from_address,
            //     'MAIL_FROM_NAME' => $request->mail_from_name,
            // ];
            // Utility::setEnvironmentValue($arrEnv);
            // Artisan::call('config:cache');
            // Artisan::call('config:clear');
            //$this->setEnvironmentValue($arrEnv);


            $post['mail_driver'] = $request->mail_driver;
            $post['mail_host'] = $request->mail_host;
            $post['mail_port'] = $request->mail_port;
            $post['mail_username'] = $request->mail_username;
            $post['mail_password'] = $request->mail_password;
            $post['mail_encryption'] = $request->mail_encryption;
            $post['mail_from_address'] = $request->mail_from_address;
            $post['mail_from_name'] = $request->mail_from_name;
            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            foreach ($post as $key => $data) {
                \DB::insert('insert into settings (`value`, `name`,`created_at`,`updated_at`) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`)', [
                    $data,
                    $key,
                    $created_at,
                    $updated_at,
                ]);
            }
            return redirect()->back()->with('success', __('Setting updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }

    public function pusherSettingStore(Request $request)
    {
        $user = \Auth::user();
        if ($user->type == 'admin') {
            $rules = [];

            if ($request->enable_chat == 'on' || $request->enable_chat == 'off') {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'pusher_app_id' => 'required|string|max:50',
                        'pusher_app_key' => 'required|string|max:50',
                        'pusher_app_key' => 'required|string|max:50',
                        'pusher_app_cluster' => 'required|string|max:50'
                    ]
                );
            }
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            // if ($request->enable_chat == 'on') {
            //     $rules['pusher_app_id']      = 'required|string|max:50';
            //     $rules['pusher_app_key']     = 'required|string|max:50';
            //     $rules['pusher_app_key']  =  required|string|max:50';
            //     $rules['pusher_app_cluster'] = 'required|string|max:50';
            // }

            // $request->validate($rules);

            // $arrEnv = [
            //     'CHAT_MODULE' => $request->enable_chat,
            //     'PUSHER_APP_ID' => $request->pusher_app_id,
            //     'PUSHER_APP_KEY' => $request->pusher_app_key,
            //     'PUSHER_APP_SECRET' => $request->pusher_app_secret,
            //     'PUSHER_APP_CLUSTER' => $request->pusher_app_cluster,
            // ];

            // //   Artisan::call('config:cache');
            // //   Artisan::call('config:clear');

            // if ($this->setEnvironmentValue($arrEnv)) {
            //     return redirect()->back()->with('success', __('Setting updated successfully'));
            // } else {
            //     return redirect()->back()->with('error', __('Something is wrong'));
            // }
            $post['enable_chat'] = $request->enable_chat;
            $post['pusher_app_id'] = $request->pusher_app_id;
            $post['pusher_app_key'] = $request->pusher_app_key;
            $post['pusher_app_secret'] = $request->pusher_app_secret;
            $post['pusher_app_cluster'] = $request->pusher_app_cluster;


            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            foreach ($post as $key => $data) {
                \DB::insert('insert into settings (`value`, `name`,`created_at`,`updated_at`) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`)', [
                    $data,
                    $key,
                    $created_at,
                    $updated_at,
                ]);
            }

            return redirect()->back()->with('success', __('Setting updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }

    public static function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                $keyPosition = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

                if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
                    $str .= "{$envKey}='{$envValue}'\n";
                } else {
                    $str = str_replace($oldLine, "{$envKey}='{$envValue}'", $str);
                }

                // if($keyPosition!=0 || !$endOfLinePosition || !$oldLine)
                // {
                //     $str .= "{$envKey}='{$envValue}'\n";
                // }
                // else
                // {
                //     $str = str_replace($oldLine, "{$envKey}='{$envValue}'", $str);
                // }
            }
        }
        $str = substr($str, 0, -1);
        $str .= "\n";

        return file_put_contents($envFile, $str) ? true : false;
    }

    public function testEmail(Request $request)
    {
        // $user = \Auth::user();
        // if ($user->type == 'admin' || $user->type == 'user') {
        //     $data                      = [];
        //     $data['mail_driver']       = $request->mail_driver;
        //     $data['mail_host']         = $request->mail_host;
        //     $data['mail_port']         = $request->mail_port;
        //     $data['mail_username']     = $request->mail_username;
        //     $data['mail_password']     = $request->mail_password;
        //     $data['mail_encryption']   = $request->mail_encryption;
        //     $data['mail_from_address'] = $request->mail_from_address;
        //     $data['mail_from_name']    = $request->mail_from_name;

        //     return view('users.test_email', compact('data'));
        // } else {
        //     return response()->json(['error' => __('Permission Denied.')], 401);
        // }
        $user = Auth::user();
        if ($user->type == 'admin') {
            $settings = Utility::getAdminPaymentSettings();
            $data = [];
            $data['mail_driver'] = $settings['mail_driver'] ? $settings['mail_driver'] : $request->mail_driver;
            $data['mail_host'] = $settings['mail_host'] ? $settings['mail_host'] : $request->mail_host;
            $data['mail_port'] = $settings['mail_port'] ? $settings['mail_port'] : $request->mail_port;
            $data['mail_username'] = $settings['mail_username'] ? $settings['mail_username'] : $request->mail_username;
            $data['mail_password'] = $settings['mail_password'] ? $settings['mail_password'] : $request->mail_password;
            $data['mail_encryption'] = $settings['mail_encryption'] ? $settings['mail_encryption'] : $request->mail_encryption;
            $data['mail_from_address'] = $settings['mail_from_address'] ? $settings['mail_from_address'] : $request->mail_from_address;
            $data['mail_from_name'] = $settings['mail_from_name'] ? $settings['mail_from_name'] : $request->mail_from_name;
            return view('users.test_email', compact('data'));
        } else if ($user->type == 'user') {
            $user = Auth::user();
            $settings = Utility::getPaymentSetting($user->currant_workspace);
            $data = [];
            $data['mail_driver'] = $settings['mail_driver'] ? $settings['mail_driver'] : $request->mail_driver;
            $data['mail_host'] = $settings['mail_host'] ? $settings['mail_host'] : $request->mail_host;
            $data['mail_port'] = $settings['mail_port'] ? $settings['mail_port'] : $request->mail_port;
            $data['mail_username'] = $settings['mail_username'] ? $settings['mail_username'] : $request->mail_username;
            $data['mail_password'] = $settings['mail_password'] ? $settings['mail_password'] : $request->mail_password;
            $data['mail_encryption'] = $settings['mail_encryption'] ? $settings['mail_encryption'] : $request->mail_encryption;
            $data['mail_from_address'] = $settings['mail_from_address'] ? $settings['mail_from_address'] : $request->mail_from_address;
            $data['mail_from_name'] = $settings['mail_from_name'] ? $settings['mail_from_name'] : $request->mail_from_name;
            return view('users.test_email', compact('data'));
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function testEmailSend(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email',
            'mail_driver' => 'required',
            'mail_host' => 'required',
            'mail_port' => 'required',
            'mail_username' => 'required',
            'mail_password' => 'required',
            'mail_from_address' => 'required',
            'mail_from_name' => 'required',
        ]);
        if ($validator->fails()) {

            $messages = $validator->getMessageBag();
            return response()->json(['success' => false, 'message' => 'Please Add The Email Credentails']);
            // return redirect()->back()->with('error', $messages->first());
        }

        try {
            config([
                'mail.driver' => $request->mail_driver,
                'mail.host' => $request->mail_host,
                'mail.port' => $request->mail_port,
                'mail.encryption' => $request->mail_encryption,
                'mail.username' => $request->mail_username,
                'mail.password' => $request->mail_password,
                'mail.from.address' => $request->mail_from_address,
                'mail.from.name' => $request->mail_from_name,
            ]);
            Mail::to($request->email)->send(new EmailTest());
        } catch (\Exception $e) {

            return response()->json([
                'is_success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'is_success' => true,
            'message' => __('Email send Successfully'),
        ]);
    }
    public function recaptchaSettingStore(Request $request)
    {
        $user = \Auth::user();
        $rules = [];

        if ($request->recaptcha_module == 'on' || $request->recaptcha_module == 'off') {
            $rules['google_recaptcha_version'] = 'required';
            $rules['google_recaptcha_key'] = 'required|string|max:50';
            $rules['google_recaptcha_secret'] = 'required|string|max:50';
        }

        $validator = \Validator::make(
            $request->all(),
            $rules
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        // $arrEnv = [
        //     'RECAPTCHA_MODULE' => $request->recaptcha_module,
        //     'NOCAPTCHA_SITEKEY' => $request->google_recaptcha_key,
        //     'NOCAPTCHA_SECRET' => $request->google_recaptcha_secret,
        // ];

        // if ($this->setEnvironmentValue($arrEnv)) {
        //     return redirect()->back()->with('success', __('Recaptcha Settings updated successfully'));
        // } else {
        //     return redirect()->back()->with('error', __('Something is wrong'));
        // }

        $post['recaptcha_module'] = $request->recaptcha_module;
        $post['google_recaptcha_version'] = $request->google_recaptcha_version;
        $post['google_recaptcha_key'] = $request->google_recaptcha_key;
        $post['google_recaptcha_secret'] = $request->google_recaptcha_secret;

        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        foreach ($post as $key => $data) {
            \DB::insert('insert into settings (`value`, `name`,`created_at`,`updated_at`) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`)', [
                $data,
                $key,
                $created_at,
                $updated_at,
            ]);
        }
        return redirect()->back()->with('success', __('Setting updated successfully'));
    }


    public function storageSettingStore(Request $request)
    {

        if (isset($request->storage_setting) && $request->storage_setting == 'local') {

            $request->validate(
                [

                    'local_storage_validation' => 'required',
                    'local_storage_max_upload_size' => 'required',
                ]
            );

            $post['storage_setting'] = $request->storage_setting;
            $local_storage_validation = implode(',', $request->local_storage_validation);
            $post['local_storage_validation'] = $local_storage_validation;
            $post['local_storage_max_upload_size'] = $request->local_storage_max_upload_size;
        }

        if (isset($request->storage_setting) && $request->storage_setting == 's3') {
            $request->validate(
                [
                    's3_key' => 'required',
                    's3_secret' => 'required',
                    's3_region' => 'required',
                    's3_bucket' => 'required',
                    's3_url' => 'required',
                    's3_endpoint' => 'required',
                    's3_max_upload_size' => 'required',
                    's3_storage_validation' => 'required',
                ]
            );
            $post['storage_setting'] = $request->storage_setting;
            $post['s3_key'] = $request->s3_key;
            $post['s3_secret'] = $request->s3_secret;
            $post['s3_region'] = $request->s3_region;
            $post['s3_bucket'] = $request->s3_bucket;
            $post['s3_url'] = $request->s3_url;
            $post['s3_endpoint'] = $request->s3_endpoint;
            $post['s3_max_upload_size'] = $request->s3_max_upload_size;
            $s3_storage_validation = implode(',', $request->s3_storage_validation);
            $post['s3_storage_validation'] = $s3_storage_validation;
        }

        if (isset($request->storage_setting) && $request->storage_setting == 'wasabi') {
            $request->validate(
                [
                    'wasabi_key' => 'required',
                    'wasabi_secret' => 'required',
                    'wasabi_region' => 'required',
                    'wasabi_bucket' => 'required',
                    'wasabi_url' => 'required',
                    'wasabi_root' => 'required',
                    'wasabi_max_upload_size' => 'required',
                    'wasabi_storage_validation' => 'required',
                ]
            );
            $post['storage_setting'] = $request->storage_setting;
            $post['wasabi_key'] = $request->wasabi_key;
            $post['wasabi_secret'] = $request->wasabi_secret;
            $post['wasabi_region'] = $request->wasabi_region;
            $post['wasabi_bucket'] = $request->wasabi_bucket;
            $post['wasabi_url'] = $request->wasabi_url;
            $post['wasabi_root'] = $request->wasabi_root;
            $post['wasabi_max_upload_size'] = $request->wasabi_max_upload_size;
            $wasabi_storage_validation = implode(',', $request->wasabi_storage_validation);
            $post['wasabi_storage_validation'] = $wasabi_storage_validation;
        }
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        foreach ($post as $key => $data) {

            $arr = [
                $data,
                $key,
                $created_at,
                $updated_at,

            ];


            \DB::insert(
                'insert into settings (`value`, `name`,`created_at`,`updated_at`) values (?, ?, ?,?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                $arr
            );
        }

        return redirect()->back()->with('success', 'Storage setting successfully updated.');
    }

    public function chatgptkey(Request $request)
    {

        if (\Auth::user()->type == 'admin') {
            $user = \Auth::user();



            if ($request->enable_chatgpt) {
                $post['enable_chatgpt'] = 'on';
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'chatgpt_key' => 'required',
                        'chatgpt_model' => 'required',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }
            } else {
                $post['enable_chatgpt'] = 'off';
            }



            if (($request->chatgpt_key) && ($request->chatgpt_key !== null)) {
                $post['chatgpt_key'] = $request->chatgpt_key;
            } else {
                return redirect()->back()->with('error', 'Please Enter ChatGpt Key !!!');
            }

            if (($request->chatgpt_model) && ($request->chatgpt_model !== null)) {
                $post['chatgpt_model'] = $request->chatgpt_model;
            } else {
                return redirect()->back()->with('error', 'Please Enter ChatGpt Model Key !!!');
            }

            unset($post['_token']);

            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            foreach ($post as $key => $data) {
                $arr = [
                    $data,
                    $key,
                    $created_at,
                    $updated_at,

                ];


                \DB::insert(
                    'insert settings (`value`, `name`,`created_at`,`updated_at`) values (?, ?, ?,?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    $arr
                );
            }

            return redirect()->back()->with('success', __('ChatGPT key successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
