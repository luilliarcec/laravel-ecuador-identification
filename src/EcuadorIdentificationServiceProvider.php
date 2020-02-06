<?php

namespace Luilliarcec\LaravelEcuadorIdentification;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Luilliarcec\LaravelEcuadorIdentification\Support\EcuadorIdentification;
use Luilliarcec\LaravelEcuadorIdentification\Validations\EcuadorIdentificationValidator;

class EcuadorIdentificationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('laravel-ecuador-identification.php'),
            ], 'config');
        }

        Validator::resolver(function ($translator, $data, $rules, $messages, $customAttributes) {
            return new EcuadorIdentificationValidator($translator, $data, $rules, $messages, $customAttributes);
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'laravel-ecuador-identification');

        // Register the main class to use with the facade
        $this->app->singleton('EcuadorIdentification', function () {
            return new EcuadorIdentification;
        });
    }
}
