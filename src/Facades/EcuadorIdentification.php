<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Facades;

use Illuminate\Support\Facades\Facade;
use Luilliarcec\LaravelEcuadorIdentification\Validations\EcuadorIdentification as Identification;

/**
 * @method static string|null validateFinalConsumer(string $identification_number)
 * @method static string|null validatePersonalIdentification(string $identification_number)
 * @method static string|null validateNaturalRuc(string $identification_number)
 * @method static string|null validatePublicRuc(string $identification_number)
 * @method static string|null validatePrivateRuc(string $identification_number)
 * @method static string|null validateRuc(string $identification_number)
 * @method static string|null validateIsNaturalPersons(string $identification_number)
 * @method static string|null validateIsJuridicalPersons(string $identification_number)
 * @method static string|null validateAllTypeIdentification(string $identification_number)
 * @method static string getError()
 * @see Identification;
 */
class EcuadorIdentification extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Identification::class;
    }
}
