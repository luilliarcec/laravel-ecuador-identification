<?php


namespace Luilliarcec\LaravelEcuadorIdentification\Support\Identifications;


use Luilliarcec\LaravelEcuadorIdentification\Contracts\IdentificationContract;
use Luilliarcec\LaravelEcuadorIdentification\Exceptions\EcuadorIdentificationException;

class NaturalRuc extends EcuadorValidations implements IdentificationContract
{
    /**
     * NaturalRuc constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->lenght = config('laravel-ecuador-identification.type-identifications.ruc.length');
        $this->billingCode = config('laravel-ecuador-identification.type-identifications.ruc.billing-code');
    }

    /**
     * Validate this identification
     *
     * @param string $number
     * @return \Illuminate\Config\Repository|mixed|string
     * @throws EcuadorIdentificationException
     */
    public function validate(string $number)
    {
        try {
            $this->validateInitial($number, $this);
            $this->validateLastDigits(substr($number, 10, 3), $this);
            $this->moduleTen($number);
        } catch (EcuadorIdentificationException $e) {
            throw new EcuadorIdentificationException($e->getMessage());
        }

        return $this->billingCode;
    }
}
