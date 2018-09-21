<?php

namespace Wcactus\CroppedImages;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/croppedimages.php' => config_path('croppedimages.php'),
			__DIR__.'/../migrations/2018_07_08_112424_create_croppedimages_tables.php' => database_path('migrations/2018_07_08_112424_create_croppedimages_tables.php')
        ], 'croppedimages');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('CroppedImages', function ($app) {
            return new Handler;
        });
    }
}
