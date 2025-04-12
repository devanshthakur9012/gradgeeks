<?php

namespace App\Http\Controllers;

use App\Models\Languages;
use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LanguagesController extends Controller
{
    public function disableLang(Request $request)
    {
        if (\Auth::user()->type == 'admin') {
            $settings = Utility::getAdminPaymentSettings();
            $disablelang = '';
            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');
            if ($request->mode == 'off') {

                if (!empty($settings['disable_lang'])) {
                    $disablelang = $settings['disable_lang'];
                    $disablelang = $disablelang . ',' . $request->lang;
                } else {
                    $disablelang = $request->lang;
                }

                \DB::insert('insert into settings (`value`, `name`,`created_at`,`updated_at`) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`)', [
                    $disablelang,
                    'disable_lang',
                    $created_at,
                    $updated_at,
                ]);

                $data['message'] = __('Language Disabled Successfully');
                $data['status'] = 200;
                return $data;
            } else {

                $disablelang = $settings['disable_lang'];
                $parts = explode(',', $disablelang);
                while (($i = array_search($request->lang, $parts)) !== false) {
                    unset($parts[$i]);
                }

                \DB::insert('insert into settings (`value`, `name`,`created_at`,`updated_at`) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`)', [
                    implode(',', $parts),
                    'disable_lang',
                    $created_at,
                    $updated_at,
                ]);

                $data['message'] = __('Language Enabled Successfully');
                $data['status'] = 200;
                return $data;
            }

        }
    }
}
