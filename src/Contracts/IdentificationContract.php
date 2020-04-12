<?php


namespace Luilliarcec\LaravelEcuadorIdentification\Contracts;


interface IdentificationContract
{
    /**
     * Validate length identification, province code, third digit, lasts digits and module validations
     *
     * @param string $identification_number Number of the identification
     * @return string|null Billing code
     */
    public function validate(string $identification_number);
}
