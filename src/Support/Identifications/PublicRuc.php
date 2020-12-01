<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Support\Identifications;

use Luilliarcec\LaravelEcuadorIdentification\Support\BaseIdentification;

class PublicRuc extends BaseIdentification
{
    public function __construct()
    {
        $this->lenght = 13;
        $this->billingCode = '04';
        $this->coefficients = [3, 2, 7, 6, 5, 4, 3, 2];
        $this->checkDigitPosition = 9;
        $this->thirdDigit = 6;
        $this->lastDigits = '0001';
    }
}
