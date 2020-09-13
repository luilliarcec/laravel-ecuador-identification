<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Support;

use Exception;
use Luilliarcec\LaravelEcuadorIdentification\Contracts\IdentificationContract;
use Luilliarcec\LaravelEcuadorIdentification\Support\Identifications\PrivateRuc;
use Luilliarcec\LaravelEcuadorIdentification\Support\Identifications\PublicRuc;

class BaseIdentification implements IdentificationContract
{
    /**
     * Number of provinces of Ecuador
     * @var int
     */
    protected $provinces = 24;

    /**
     * Length of the different types of identification
     * @var int
     */
    protected $lenght = 0;

    /**
     * Billing code for identification types
     * @var string|null
     */
    protected $billingCode = null;

    /**
     * Third digit of the identification number
     * @var int
     */
    protected $thirdDigit;

    /**
     * Lasts digits of the identification number
     * @var string
     */
    protected $lastDigits = '';

    /**
     * Represents the position of the verifying digit in the identification number
     * @var int
     */
    protected $checkDigitPosition;

    /**
     * Represents the check coefficients for the identification number
     * @var array
     */
    protected $coefficients;

    /**
     * Validate length identification, province code, third digit, lasts digits and module validations
     *
     * @param string $identification_number Identification document
     * @return string|null Billing code or null
     * @throws Exception
     */
    public function validate(string $identification_number)
    {
        $this->lenghtValidation($identification_number);
        $this->provinceCodeValidation($identification_number);
        $this->thirdDigitValidation($identification_number);
        $this->lastsDigitsValidation($identification_number);

        if ($this instanceof PublicRuc || $this instanceof PrivateRuc) {
            $this->moduleElevenValidation($identification_number);
        } else {
            $this->moduleTenValidation($identification_number);
        }

        return $this->billingCode;
    }

    /**
     * Initial validation of the identification, not empty, only digits, not less than the given length.
     *
     * @param string $identification_number Identification document
     * @throws Exception
     */
    protected function lenghtValidation(string $identification_number): void
    {
        if (empty($identification_number)) {
            throw new Exception('Field must have a value.');
        }

        if (!ctype_digit($identification_number)) {
            throw new Exception('Field must be digits.');
        }

        if (strlen($identification_number) != $this->lenght) {
            throw new Exception("Field must be {$this->lenght} digits.");
        }
    }

    /**
     * Validate the province code (first two numbers of CI/RUC)
     * The first 2 positions correspond to the province where it was issued,
     * so the first two numbers will not be greater than 24 or less than 1
     *
     * @param string $identification_number Identification document
     * @throws Exception
     */
    protected function provinceCodeValidation(string $identification_number): void
    {
        $code = $this->getProvinceCodeValue($identification_number);

        if ($code < 1 || $code > $this->provinces) {
            throw new Exception("In your province code must be between 01 and {$this->provinces}.");
        }
    }

    /**
     * Valid the third digit
     *
     * @param string $identification_number Identification document
     * @throws Exception
     */
    protected function thirdDigitValidation(string $identification_number): void
    {
        $third_digit = $this->getThirdDigitValue($identification_number);

        if ($third_digit != $this->thirdDigit) {
            throw new Exception("Field must have the third digit equal to {$this->thirdDigit}.");
        }
    }

    /**
     * Valid the lasts digits
     *
     * @param string $identification_number Identification document
     * @throws Exception
     */
    protected function lastsDigitsValidation(string $identification_number): void
    {
        $lasts_digits = $this->getLastsDigitsValue($identification_number);

        if ($lasts_digits != $this->lastDigits) {
            throw new Exception("Field does not have the last digits equal to {$this->lastDigits}.");
        }
    }

    /**
     * Module 10 Algorithm to validate if Certificates and RUC of natural person are valid.
     *
     * @param string $identification_number Identification document
     * @throws Exception The verified digit does not match the verification digit.
     */
    protected function moduleTenValidation(string $identification_number): void
    {
        $check_digit_value = $this->getCheckDigitValue($identification_number);
        $numbers = $this->getNumbersAsArray($identification_number);


        $total = 0;

        foreach ($numbers as $key => $value) {
            $proceeds = ($value * $this->coefficients[$key]);

            if ($proceeds >= 10) {
                $proceeds = array_sum(str_split($proceeds));
            }

            $total += $proceeds;
        }

        $residue = $total % 10;

        $verified_digit_value = $residue == 0 ? $residue : 10 - $residue;

        if ($verified_digit_value != $check_digit_value) {
            throw new Exception('The identification number is invalid.');
        }
    }

    /**
     * Module 11 Algorithm to validate if RUC of Public Companies and Private Companies are valid.
     *
     * @param string $identification_number Identification document
     * @throws Exception The verified digit does not match the verification digit.
     */
    protected function moduleElevenValidation(string $identification_number): void
    {
        $check_digit_value = $this->getCheckDigitValue($identification_number);
        $numbers = $this->getNumbersAsArray($identification_number);

        $total = 0;

        foreach ($numbers as $key => $value) {
            $proceeds = ($value * $this->coefficients[$key]);
            $total += $proceeds;
        }

        $residue = $total % 11;

        $verified_digit_value = $residue == 0 ? $residue : 11 - $residue;

        if ($verified_digit_value != $check_digit_value) {
            throw new Exception('The identification number is invalid.');
        }
    }

    /**
     * Gets the province code value
     *
     * @param string $identification_number Identification document
     * @return false|string Value of the province code number
     */
    protected function getProvinceCodeValue(string $identification_number)
    {
        return substr($identification_number, 0, 2);
    }

    /**
     * Gets the third digit number
     *
     * @param string $identification_number Identification document
     * @return false|string Value of the third digit number
     */
    protected function getThirdDigitValue(string $identification_number)
    {
        return substr($identification_number, 2, 1);
    }

    /**
     * Gets the lasts digits value
     *
     * @param string $identification_number Identification document
     * @return false|string Value of the lasts digits
     */
    protected function getLastsDigitsValue(string $identification_number)
    {
        return substr($identification_number, $this->lenght - strlen($this->lastDigits), strlen($this->lastDigits));
    }

    /**
     * Gets the value of the verification number
     *
     * @param string $identification_number Identification document
     * @return false|string Value of the verification number
     */
    protected function getCheckDigitValue(string $identification_number)
    {
        return substr($identification_number, $this->checkDigitPosition - 1, 1);
    }

    /**
     * Get identification numbers for verification as Array
     *
     * @param string $identification_number Identification document
     * @return array Identification numbers for verification
     */
    protected function getNumbersAsArray(string $identification_number): array
    {
        return str_split(substr($identification_number, 0, $this->lenght - (strlen($this->lastDigits) + 1)));
    }
}
