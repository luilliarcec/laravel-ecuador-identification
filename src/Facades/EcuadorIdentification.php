<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Facades;

use Illuminate\Support\Facades\Facade;
use Luilliarcec\LaravelEcuadorIdentification\Support\EcuadorIdentification as Identification;

/**
 * @method static string|null validatePersonalIdentification(string $number)
 * @method static string|null validateNaturalPersonRuc(string $number)
 * @method static string|null validatePrivateCompanyRuc(string $number)
 * @method static string|null validatePublicCompanyRuc(string $number)
 * @method static string|null validateFinalConsumer(string $number)
 * @method static string|null validateIsNaturalPersons(string $number)
 * @method static string|null validateIsJuridicalPersons(string $number)
 * @method static string|null validateAllIdentificatons(string $number)
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
