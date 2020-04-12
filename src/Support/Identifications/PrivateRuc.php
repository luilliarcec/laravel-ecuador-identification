<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Support\Identifications;

use Luilliarcec\LaravelEcuadorIdentification\Support\BaseIdentification;

class PrivateRuc extends BaseIdentification
{
    /**
     * PrivateRuc constructor.
     */
    public function __construct()
    {
        $this->lenght = 13;
        $this->billingCode = '04';
        $this->coefficients = [4, 3, 2, 7, 6, 5, 4, 3, 2];
        $this->checkDigitPosition = 10;
        $this->thirdDigit = 9;
        $this->lastDigits = '001';
    }
}
