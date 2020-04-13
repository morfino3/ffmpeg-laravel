<?php

namespace Laboratory\Covid;

use Illuminate\Support\ServiceProvider;
use Laboratory\Covid\Covid;

class CovidServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/covid.php' => config_path('covid.php'),
        ], 'covid');

    }

    /**
     * Register the service provider.
     *
     * @throws \Exception
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/covid.php', 'covid'
        );

        $this->app->singleton('covid', function ($app) {
            $dependency = $app['config']->get('covid');
            return new Covid(\FFMpeg\FFMpeg::create($dependency));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'covid'
        ];
    }

}