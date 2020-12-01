<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Tests;

use Luilliarcec\LaravelEcuadorIdentification\EcuadorIdentificationServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [EcuadorIdentificationServiceProvider::class];
    }
}
