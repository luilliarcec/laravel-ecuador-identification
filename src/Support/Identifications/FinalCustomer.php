<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Support\Identifications;

use Luilliarcec\LaravelEcuadorIdentification\Support\BaseIdentification;

class FinalCustomer extends BaseIdentification
{
    public function __construct()
    {
        $this->billingCode = '07';
    }

    public function validate(string $identification_number): string
    {
        if ($identification_number != "9999999999999") {
            throw new \Exception('The identification number is invalid.');
        }

        return $this->billingCode;
    }
}
