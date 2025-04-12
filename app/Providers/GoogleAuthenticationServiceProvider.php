<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class GoogleAuthenticationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            base_path('config/google2fa.php'),
            'google2fa'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(base_path('routes/web.php'));
        $this->loadViewsFrom(base_path('resources/views'), 'auth');
        $this->loadMigrationsFrom(base_path('database/migrations'));
        $this->autoPublishConfig();
    }

    protected function autoPublishConfig()
    {
        $configPath = config_path('google2fa.php');
        $sourcePath = base_path('config/google2fa.php');
        if (!File::exists($configPath)) {
            File::copy($sourcePath, $configPath);
        }
    }
}
