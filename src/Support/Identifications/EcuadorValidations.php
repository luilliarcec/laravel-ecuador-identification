<?php


namespace Luilliarcec\LaravelEcuadorIdentification\Support\Identifications;

use Luilliarcec\LaravelEcuadorIdentification\Contracts\IdentificationContract;
use Luilliarcec\LaravelEcuadorIdentification\Exceptions\EcuadorIdentificationException;

class EcuadorValidations
{
    /**
     * Number of provinces of Ecuador
     *
     * @var \Illuminate\Config\Repository
     */
    protected $provinces;

    /**
     * Length of the different types of identification
     *
     * @var \Illuminate\Config\Repository|mixed|string
     */
    protected $lenght;

    /**
     * Billing code for identification types
     *
     * @var \Illuminate\Config\Repository|mixed|string
     */
    protected $billingCode;

    /**
     * EcuadorValidations constructor.
     */
    public function __construct()
    {
        $this->provinces = config('laravel-ecuador-identification.provinces');
    }

    /**
     * Validate length identification, province code, third digit
     *
     * @param $number
     * @param IdentificationContract $type
     * @throws EcuadorIdentificationException
     */
    protected function validateInitial($number, IdentificationContract $type)
    {
        try {
            $this->validateLength($number, (int)$this->lenght);
            $this->validateProvinceCode(substr($number, 0, 2));
            $this->validateThirdDigit($number[2], $type);
        } catch (EcuadorIdentificationException $e) {
            throw new EcuadorIdentificationException($e->getMessage());
        }
    }

    /**
     * Initial validation of the identification, not empty, only digits, not less than the given length.
     *
     * @param string $value CI or RUC
     * @param int $len Number of characters required
     * @return bool
     * @throws EcuadorIdentificationException When the value is empty, when the value isn't digits and
     * when the value doesn't have the required length
     */
    private function validateLength($value, $len)
    {
        if (empty($value)) {
            throw new EcuadorIdentificationException('Field must have a value.');
        }

        if (!ctype_digit($value)) {
            throw new EcuadorIdentificationException('Must be digits.');
        }

        if (strlen($value) != $len) {
            throw new EcuadorIdentificationException("Must be {$len} digits.");
        }

        return true;
    }

    /**
     * Validate the province code (first two numbers of CI/RUC)
     * The first 2 positions correspond to the province where it was issued,
     * so the first two numbers will not be greater than 24 or less than 1
     *
     * @param string $value First two numbers of CI/RUC
     * @return boolean
     * @throws EcuadorIdentificationException When the province code is not between 1 and 24
     */
    private function validateProvinceCode($value)
    {
        if ($value < 1 || $value > $this->provinces) {
            throw new EcuadorIdentificationException("In your province code must be between 01 and {$this->provinces}.");
        }

        return true;
    }

    /**
     * Valid the third digit
     *
     * It allows the third digit of the document to be valid.
     * Depending on the type field (type of identification) validations are performed.
     *
     * NATURAL_PERSON
     * For Certificates and RUC of natural persons the third digit is less than 6 so
     * it must be between 0 and 5 (0,1,2,3,4,5)
     *
     * PRIVATE_COMPANY
     * For RUC of private companies the third digit must be equal to 9.
     *
     * PUBLIC_COMPANY
     * For RUC of public companies the third digit must be equal to 6.
     *
     * @param string $value Third digit of CI/RUC
     * @param IdentificationContract $type Type of identifier
     * @return boolean
     * @throws EcuadorIdentificationException When it does not comply with the validation according to the type of identifier
     */
    private function validateThirdDigit($value, IdentificationContract $type)
    {
        if ($type instanceof NaturalRuc || $type instanceof PersonalIdentification) {
            $min = config('laravel-ecuador-identification.personal-identification.third-digit.min');
            $max = config('laravel-ecuador-identification.personal-identification.third-digit.max');

            if ($value < $min || $value > $max)
                throw new EcuadorIdentificationException("Field must have the third digit between {$min} and {$max}.");

            return true;
        }

        if ($type instanceof PrivateRuc) {
            $thirdDigit = config('laravel-ecuador-identification.private-ruc.third-digit');
        } elseif ($type instanceof PublicRuc) {
            $thirdDigit = config('laravel-ecuador-identification.public-ruc.third-digit');
        } else {
            throw new EcuadorIdentificationException('Field does not have this type of identification.');
        }

        if ($value != $thirdDigit)
            throw new EcuadorIdentificationException("Field must have the third digit equal to {$thirdDigit}.");

        return true;
    }

    /**
     * Validation of the last digits
     *
     * Public Ruc => 0001
     * Other Ruc => 001
     *
     * @param string $value The last digits
     * @param IdentificationContract $type Type of identifier
     * @return boolean
     * @throws EcuadorIdentificationException When not equal to 001
     */
    protected function validateLastDigits($value, IdentificationContract $type)
    {
        if ($type instanceof NaturalRuc) {
            $lastDigits = config('laravel-ecuador-identification.natural-ruc.last-digits');
        } elseif ($type instanceof PrivateRuc) {
            $lastDigits = config('laravel-ecuador-identification.private-ruc.last-digits');
        } elseif ($type instanceof PublicRuc) {
            $lastDigits = config('laravel-ecuador-identification.public-ruc.last-digits');
        } else {
            throw new EcuadorIdentificationException('Field does not have this type of identification.');
        }

        if ($value != $lastDigits) {
            throw new EcuadorIdentificationException("Field does not have the last digits equal to {$lastDigits}");
        }

        return true;
    }

    /**
     * Module 10 Algorithm to validate if Certificates and RUC of natural person are valid.
     *
     * Coefficients used to validate the tenth digit of the Certificates,
     * are:  2, 1, 2, 1, 2, 1, 2, 1, 2
     *
     * Step 1: Multiply each digit of the card by the coefficient,
     * except for the verification digit (tenth digit),
     * if it is greater than 10 sums between digits. Example:
     *
     * 2  1  2  1  2  1  2  1  2  (Coefficients)
     * 1  7  1  0  0  3  4  0  6  (Certificate)
     * 2  7  2  0  0  3  8  0  [12] => continue to step 2.
     *
     * Step 2: If any of the multiplication results is greater than 10,
     * it is added between digits of the result. Example: [12] => 1 + 2 = Result (3)
     *
     * Step 3: The result of the multiplications is added. Example:
     * 2  7  2  0  0  3  8  0  3 = Result (25)
     *
     * Step 4: The result of the sum is divided by 10 and the remainder of the division is obtained
     * If the remainder is 0 the check digit is 0
     * Otherwise, the residue is subtracted from 10
     *
     * If the result is equal to the verification digit, the value is correct.
     *
     * @param string $number Certificates or RUC of natural person
     * @return boolean
     * @throws EcuadorIdentificationException The verified digit does not match the verification digit.
     */
    protected function moduleTen($number)
    {
        $check_digit_position = config('laravel-ecuador-identification.personal-identification.check-digit-position');
        $coefficients = config('laravel-ecuador-identification.personal-identification.coefficients');

        $check_digit_value = $number[$check_digit_position - 1];
        $numbers = str_split(substr($number, 0, 9));

        $total = 0;

        foreach ($numbers as $key => $value) {
            $proceeds = ($value * $coefficients[$key]);

            if ($proceeds >= 10) {
                $proceeds = str_split($proceeds);
                $proceeds = array_sum($proceeds);
            }

            $total += $proceeds;
        }

        $residue = $total % 10;

        $verified_digit_value = $residue == 0 ? 0 : 10 - $residue;

        if ($verified_digit_value != $check_digit_value) {
            throw new EcuadorIdentificationException('Field is invalid');
        }

        return true;
    }

    /**
     * Module 11 Algorithm to validate if RUC of Public Companies and Private Companies are valid.
     *
     * For Public Companies (Third Digit => [6]):
     * => The verifier digit is the ninth digit.
     * => Coefficients used to validate the ninth digit of the Public Company RUC, (Third digit = [6])
     * are:  3, 2, 7, 6, 5, 4, 3, 2
     *
     *
     * For Private Companies (Third Digit => [9]):
     * => The verifier digit is the tenth digit.
     * => Coefficients used to validate the tenth digit of the Private Company RUC, (Third digit = [9])
     * are:  4, 3, 2, 7, 6, 5, 4, 3, 2
     *
     * Step 1: Multiply each digit of the RUC with its respective coefficient,
     * except the verification digit. Example:
     *
     * Public Companies
     * 3   2   7   6  5  4  3  2  (Coefficients)
     * 1   7   6   0  0  0  1  0  [4]  0  0  0  1  (Public RUC) [4] => Ninth Digit (Check Digit)
     * 3   14  42  0  0  0  3  0 => Multiplication Result
     *
     * Private Companies
     * 4   3   2   7   6   5   4   3   2  (Coefficients)
     * 1   7   9   0   0   8   5   7   8   [3]  0  0  1   (Private RUC) [3] => Tenth Digit (Check Digit)
     * 4   21  18  0   0   40  20  21  16 => Multiplication Result
     *
     * Step 2: The multiplication results are added
     *
     * Public Companies
     * 3  14  42  0  0  0  3  0 = Result (62)
     *
     * * Private Companies
     * 4  21  18  0  0  40  20  21  16 = Result (140)
     *
     * Step 3: The result of the sum is divided to 11 and the remainder of the division is obtained.
     * If the remainder is 0 the check digit is 0
     * Otherwise, the residue is subtracted from 11
     *
     * If the result is equal to the verification digit, the value is correct.
     *
     * @param string $number Private Company RUC or Public Compnay RUC
     * @param IdentificationContract $type Type of identifier
     * @return boolean
     * @throws EcuadorIdentificationException The verified digit does not match the verification digit.
     */
    protected function moduleEleven($number, IdentificationContract $type)
    {
        if ($type instanceof PrivateRuc) {
            $coefficients = config('laravel-ecuador-identification.private-ruc.coefficients');
            $check_digit_position = config('laravel-ecuador-identification.private-ruc.check-digit-position');
            $check_digit_value = $number[$check_digit_position - 1];
            $numbers = str_split(substr($number, 0, 9));
        } elseif ($type instanceof PublicRuc) {
            $coefficients = config('laravel-ecuador-identification.public-ruc.coefficients');
            $check_digit_position = config('laravel-ecuador-identification.public-ruc.check-digit-position');
            $check_digit_value = $number[$check_digit_position - 1];
            $numbers = str_split(substr($number, 0, 8));
        } else {
            throw new EcuadorIdentificationException('Field does not have this type of identification.');
        }

        $total = 0;

        foreach ($numbers as $key => $value) {
            $proceeds = ($value * $coefficients[$key]);
            $total += $proceeds;
        }

        $residue = $total % 11;

        $verified_digit_value = $residue == 0 ? 0 : 11 - $residue;

        if ($verified_digit_value != $check_digit_value) {
            throw new EcuadorIdentificationException('Field is invalid');
        }

        return true;
    }
}
