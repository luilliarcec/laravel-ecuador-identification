<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Support\Identifications;

use Luilliarcec\LaravelEcuadorIdentification\Exceptions\IdentificationException;
use Luilliarcec\LaravelEcuadorIdentification\Support\BaseIdentification;

class PersonalIdentification extends BaseIdentification
{
    /**
     * PersonalIdentification constructor.
     */
    public function __construct()
    {
        $this->lenght = 10;
        $this->billingCode = '05';
        $this->coefficients = [2, 1, 2, 1, 2, 1, 2, 1, 2];
        $this->checkDigitPosition = 10;
        $this->thirdDigit = 5;
    }

    protected function thirdDigitValidation(string $identification_number): void
    {
        $third_digit = $this->getThirdDigitValue($identification_number);

        if ($third_digit > $this->thirdDigit) {
            throw new IdentificationException("Field must have the third digit less than or equal to {$this->thirdDigit}.");
        }
    }
}
