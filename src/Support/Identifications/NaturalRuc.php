<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Support\Identifications;

use Luilliarcec\LaravelEcuadorIdentification\Support\BaseIdentification;

class NaturalRuc extends BaseIdentification
{
    /**
     * NaturalRuc constructor.
     */
    public function __construct()
    {
        $this->lenght = 13;
        $this->billingCode = '04';
        $this->coefficients = [2, 1, 2, 1, 2, 1, 2, 1, 2];
        $this->checkDigitPosition = 10;
        $this->thirdDigit = 5;
        $this->lastDigits = '001';
    }

    protected function thirdDigitValidation(string $identification_number): void
    {
        $third_digit = $this->getThirdDigitValue($identification_number);

        if ($third_digit > $this->thirdDigit) {
            throw new \Exception("Field must have the third digit less than or equal to {$this->thirdDigit}.");
        }
    }
}
