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
        Validator::resolver(function ($translator, $data, $rules, $messages, $customAttributes) {
            return new EcuadorIdentificationValidator($translator, $data, $rules, $messages, $customAttributes);
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Register the main class to use with the facade
        $this->app->singleton('EcuadorIdentification', function () {
            return new EcuadorIdentification;
        });
    }
}
