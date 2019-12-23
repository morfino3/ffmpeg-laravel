<?php

namespace Laboratory\Vidconvert;

use Exception;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class VidconvertServiceProvider extends IlluminateServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/laboratory.vidconvert.php' => config_path('laboratory.bucket.php'),
        ], 'laboratory-vidconvert:config');

        // $this->publishes([
        //      __DIR__ . '/../migrations/' => database_path('/migrations'),
        //  ], 'laboratory-bucket:migrations');

        $this->mergeConfigFrom(
            __DIR__ . '/../config/laboratory.vidconvert.php', 'laboratory.vidconvert'
        );
    }

    /**
     * Register the service provider.
     *
     * @throws \Exception
     * @return void
     */
    public function register()
    {
        $this->app->bind('vidconvert.model', function($app) {
            $asset = $app['config']->get('laboratory.vidconvert.model.asset');
            $model = new $asset;
            $model->setConnection($app['config']->get('database.default'));

            return $model;
        });

        $this->app->bind('vidconvert', function($app) {
            $path = $app['config']->get('laboratory.vidconvert.storage.path');

            return new Bucket(
                $app['vidconvert.model'],
                $path
            );
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
            'vidconvert',
            'vidconvert.model'
        ];
    }

}

