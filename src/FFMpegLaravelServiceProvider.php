<?php

namespace FFMpegLaravel\FFMpegLaravel;

use Illuminate\Support\ServiceProvider;
use FFMpegLaravel\FFmpegLaravel\FFMpegLaravel;

class FFMpegLaravelServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/ffmpeglaravel.php' => config_path('ffmpeglaravel.php'),
        ], 'ffmpeglaravel');

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
            __DIR__ . '/../config/ffmpeglaravel.php', 'ffmpeglaravel'
        );

        $this->app->singleton('ffmpeglaravel', function ($app) {
            $dependency = $app['config']->get('ffmpeglaravel');
            return new FFmpegLaravel(\FFMpeg\FFMpeg::create($dependency));
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
            'ffmpeglaravel'
        ];
    }

}