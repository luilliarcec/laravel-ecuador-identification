<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Tests;

use Orchestra\Testbench\TestCase;
use Luilliarcec\LaravelEcuadorIdentification\LaravelEcuadorIdentificationServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [LaravelEcuadorIdentificationServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
