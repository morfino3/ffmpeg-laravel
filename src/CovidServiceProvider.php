<?php

namespace Laboratory\Covid;

use Laboratory\Covid\Covid;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class CovidServiceProvider extends IlluminateServiceProvider
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
        ], 'laboratory-covid:config');

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
            return $app->make(Covid::class);
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
            'covid',
            'covid.model'
        ];
    }

}

