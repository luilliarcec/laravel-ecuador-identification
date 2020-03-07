<?php


namespace Luilliarcec\LaravelEcuadorIdentification\Contracts;


interface IdentificationContract
{
    /**
     * Validate the identification
     *
     * @param string $number
     * @return string
     */
    public function validate(string $number);
}
