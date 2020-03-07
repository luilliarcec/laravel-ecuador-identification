<?php


namespace Luilliarcec\LaravelEcuadorIdentification\Support\Identifications;


use Luilliarcec\LaravelEcuadorIdentification\Contracts\IdentificationContract;
use Luilliarcec\LaravelEcuadorIdentification\Exceptions\EcuadorIdentificationException;

class PersonalIdentification extends EcuadorValidations implements IdentificationContract
{
    /**
     * PersonalIdentification constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->lenght = config('laravel-ecuador-identification.type-identifications.personal-identification.length');
        $this->billingCode = config('laravel-ecuador-identification.type-identifications.personal-identification.billing-code');
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
            $this->moduleTen($number);
        } catch (EcuadorIdentificationException $e) {
            throw new EcuadorIdentificationException($e->getMessage());
        }

        return $this->billingCode;
    }
}
