<?php

namespace Luilliarcec\LaravelEcuadorIdentification;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Luilliarcec\LaravelEcuadorIdentification\Skeleton\SkeletonClass
 */
class LaravelEcuadorIdentificationFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-ecuador-identification';
    }
}
