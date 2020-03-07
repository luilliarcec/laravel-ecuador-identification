<?php


namespace Luilliarcec\LaravelEcuadorIdentification\Support\Identifications;


use Luilliarcec\LaravelEcuadorIdentification\Contracts\IdentificationContract;
use Luilliarcec\LaravelEcuadorIdentification\Exceptions\EcuadorIdentificationException;

class FinalCustomer extends EcuadorValidations implements IdentificationContract
{
    /**
     * FinalCustomer constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->billingCode = config('laravel-ecuador-identification.type-identifications.final-customer.billing-code');
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
            if ($number != config('laravel-ecuador-identification.final-customer.unique-value')) {
                throw new EcuadorIdentificationException("Field is invalid");
            }
        } catch (EcuadorIdentificationException $e) {
            throw new EcuadorIdentificationException($e->getMessage());
        }

        return $this->billingCode;
    }
}
