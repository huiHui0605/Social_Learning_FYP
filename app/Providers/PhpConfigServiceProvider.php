<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PhpConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Apply PHP configuration overrides
        $phpConfig = config('app.php_config', []);
        
        foreach ($phpConfig as $setting => $value) {
            if (ini_get($setting) !== false) {
                ini_set($setting, $value);
            }
        }
        
        // Log the applied settings for debugging
        \Log::info('PHP Configuration applied', [
            'post_max_size' => ini_get('post_max_size'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit'),
        ]);
    }
}
