<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Utility;
use Illuminate\Support\Facades\Schema;

class pusher_config
{
    public function handle(Request $request, Closure $next): Response
    {
        if (file_exists(storage_path() . "/installed") && Schema::hasTable('settings') === true) {
            $settings = Utility::getPusherSetting();
            if ($settings) {
                config([
                    'chatify.pusher.key' => isset($settings['pusher_app_key']) ? $settings['pusher_app_key'] : '',
                    'chatify.pusher.secret' => isset($settings['pusher_app_secret']) ? $settings['pusher_app_secret'] : '',
                    'chatify.pusher.app_id' => isset($settings['pusher_app_id']) ? $settings['pusher_app_id'] : '',
                    'chatify.pusher.options.cluster' => isset($settings['pusher_app_cluster']) ? $settings['pusher_app_cluster'] : '',
                ]);
            }
        }

        return $next($request);
    }
}
