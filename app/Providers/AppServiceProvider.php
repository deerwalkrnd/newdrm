<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
        if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }

        /**
            * Somehow PHP is not able to write in default /tmp directory and SwiftMailer was failing.
            * To overcome this situation, we set the TMPDIR environment variable to a new value.
        */
        
        // if (class_exists('Swift_Preferences')) {
        //     \Swift_Preferences::getInstance()->setTempDir(storage_path().'/tmp');
        // } else {
        //     \Log::warning('Class Swift_Preferences does not exists');
        // }
    }
}
