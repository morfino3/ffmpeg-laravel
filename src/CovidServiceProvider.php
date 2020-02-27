<?php

namespace Laboratory\Covid;

use Laboratory\Covid\FFMpeg;
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
            __DIR__ . '/../config/laboratory.covid.php' => config_path('laboratory.bucket.php'),
        ], 'laboratory-covid:config');

        // $this->publishes([
        //      __DIR__ . '/../migrations/' => database_path('/migrations'),
        //  ], 'laboratory-bucket:migrations');
    }

    /**
     * Register the service provider.
     *
     * @throws \Exception
     * @return void
     */
    public function register()
    {
        // $this->app->bind('vidconvert.model', function($app) {
        //     $asset = $app['config']->get('laboratory.vidconvert.model.asset');
        //     $model = new $asset;
        //     $model->setConnection($app['config']->get('database.default'));

        //     return $model;
        // });

        $this->mergeConfigFrom(
            __DIR__ . '/../config/laboratory.covid.php', 'laboratory.covid'
        );

        $this->app->singleton('covid', function ($app) {
            return $app->make(FFMpeg::class);
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

