<?php

namespace Luilliarcec\LaravelEcuadorIdentification;

use Illuminate\Support\ServiceProvider;
use Luilliarcec\LaravelEcuadorIdentification\Support\EcuadorIdentification;

class EcuadorIdentificationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-ecuador-identification');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-ecuador-identification');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-ecuador-identification.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-ecuador-identification'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel-ecuador-identification'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-ecuador-identification'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-ecuador-identification');

        // Register the main class to use with the facade
        $this->app->singleton('EcuadorIdentification', function () {
            return new EcuadorIdentification;
        });
    }
}
