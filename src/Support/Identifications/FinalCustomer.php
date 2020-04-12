<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Support\Identifications;

use Luilliarcec\LaravelEcuadorIdentification\Exceptions\IdentificationException;
use Luilliarcec\LaravelEcuadorIdentification\Support\BaseIdentification;

class FinalCustomer extends BaseIdentification
{
    /**
     * FinalCustomer constructor.
     */
    public function __construct()
    {
        $this->billingCode = '07';
    }

    public function validate(string $identification_number): string
    {
        if ($identification_number != "9999999999999") {
            throw new IdentificationException('The identification number is invalid.');
        }

        return $this->billingCode;
    }
}
