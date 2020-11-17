<?php

namespace Luilliarcec\LaravelEcuadorIdentification;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Luilliarcec\LaravelEcuadorIdentification\Validations\EcuadorIdentification;

class EcuadorIdentificationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        Validator::extendImplicit(
            'ecuador',
            '\Luilliarcec\LaravelEcuadorIdentification\Validations\EcuadorIdentification@validate',
            'The :attribute field is invalid.'
        );
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
