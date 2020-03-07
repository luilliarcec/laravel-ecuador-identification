<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Support;

use Luilliarcec\LaravelEcuadorIdentification\Exceptions\EcuadorIdentificationException;

/**
 * Class to validate Ecuadorian identity card, Natural ruc, Private ruc and Public ruc
 *
 * @link https://www.sri.gob.ec/web/guest/RUC#%C2%BFc%C3%B3mo-se
 * @link http://www.sri.gob.ec/DocumentosAlfrescoPortlet/descargar/1ee224e6-b84b-4a8f-8127-59f8cd99ae58/LOGARITMO_VALIDA_RUC.docx
 * @package Luilliarcec\LaravelEcuadorIdentification\Support
 */
class EcuadorIdentification
{
    /**
     * Natural person ruc
     */
    const NaturalPerson = 'NaturalPerson';

    /**
     * Private company ruc
     */
    const PrivateCompany = 'PrivateCompany';

    /**
     * Public company ruc
     */
    const PublicCompany = 'PublicCompany';

    /**
     * Error encapsulator variable
     *
     * @var string
     */
    private $error;

    /**
     * Number of provinces of Ecuador
     *
     * @var \Illuminate\Config\Repository
     */
    private $provinces;

    /**
     * Length of the different types of identification
     *
     * @var array
     */
    private $lenght;

    /**
     * Billing code for identification types
     *
     * @var array
     */
    private $billingCode;

    public function __construct()
    {
        $this->provinces = config('laravel-ecuador-identification.provinces');

        $this->lenght = [
            'ruc' => config('laravel-ecuador-identification.type-identifications.ruc.length'),
            'personal-identification' => config('laravel-ecuador-identification.type-identifications.personal-identification.length'),
        ];

        $this->billingCode = [
            'personal-identification' => config('laravel-ecuador-identification.type-identifications.personal-identification.billing-code'),
            'ruc' => config('laravel-ecuador-identification.type-identifications.ruc.billing-code'),
            'final-customer' => config('laravel-ecuador-identification.type-identifications.final-customer.billing-code'),
        ];
    }

    /**
     * Set Error
     *
     * @param string $error
     */
    protected function setError(string $error): void
    {
        $this->error = $error;
    }

    /**
     * Get Error
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * Validates the Ecuadorian Identification Card
     *
     * @param string $number Number of Identification Card
     * @return string|null
     */
    public function validatePersonalIdentification($number)
    {
        $this->setError('');

        try {
            $this->initValidation($number, $this->lenght['personal-identification']);
            $this->provinceCodeValidation(substr($number, 0, 2));
            $this->thirdDigitValidation($number[2], self::NaturalPerson);
            $this->moduleTen($number);
        } catch (EcuadorIdentificationException $e) {
            $this->setError($e->getMessage());
            return null;
        }

        return $this->billingCode['personal-identification'];
    }

    /**
     * Validates the Ecuadorian RUC of Natural Person
     *
     * @param string $number Number of RUC Natural Person
     * @return string|null
     */
    public function validateNaturalPersonRuc($number)
    {
        $this->setError('');

        try {
            $this->initValidation($number, $this->lenght['ruc']);
            $this->provinceCodeValidation(substr($number, 0, 2));
            $this->thirdDigitValidation($number[2], self::NaturalPerson);
            $this->theLastDigitsValidation(substr($number, 10, 3), self::NaturalPerson);
            $this->moduleTen($number);
        } catch (EcuadorIdentificationException $e) {
            $this->setError($e->getMessage());
            return null;
        }

        return $this->billingCode['ruc'];
    }

    /**
     * Validates the Ecuadorian RUC of Private Companies
     *
     * @param string $number Number of RUC Private Companies
     * @return string|null
     */
    public function validatePrivateCompanyRuc($number)
    {
        $this->setError('');

        try {
            $this->initValidation($number, $this->lenght['ruc']);
            $this->provinceCodeValidation(substr($number, 0, 2));
            $this->thirdDigitValidation($number[2], self::PrivateCompany);
            $this->theLastDigitsValidation(substr($number, 10, 3), self::PrivateCompany);
            $this->moduleEleven($number, self::PrivateCompany);
        } catch (EcuadorIdentificationException $e) {
            $this->setError($e->getMessage());
            return null;
        }

        return $this->billingCode['ruc'];
    }

    /**
     * Validates the Ecuadorian RUC of Public Companies
     *
     * @param string $number Number of RUC Public Companies
     * @return string|null
     */
    public function validatePublicCompanyRuc($number)
    {
        $this->setError('');

        try {
            $this->initValidation($number, $this->lenght['ruc']);
            $this->provinceCodeValidation(substr($number, 0, 2));
            $this->thirdDigitValidation($number[2], self::PublicCompany);
            $this->theLastDigitsValidation(substr($number, 9, 4), self::PublicCompany);
            $this->moduleEleven($number, self::PublicCompany);
        } catch (EcuadorIdentificationException $e) {
            $this->setError($e->getMessage());
            return null;
        }

        return $this->billingCode['ruc'];
    }

    /**
     * Validates the Ecuadorian Final Consumer
     *
     * @param $number
     * @return string|null
     */
    public function validateFinalConsumer($number)
    {
        $this->setError('');

        try {
            if ($number != config('laravel-ecuador-identification.final-customer.unique-value')) {
                throw new EcuadorIdentificationException("Field is invalid");
            }
        } catch (EcuadorIdentificationException $e) {
            $this->setError($e->getMessage());
            return null;
        }

        return $this->billingCode['final-customer'];
    }

    /**
     * Validate that the number belongs to natural persons.
     *
     * @param $number
     * @return string|null
     */
    public function validateIsNaturalPersons($number)
    {
        return $this->validatePersonalIdentification($number) !== null ?
            $this->validatePersonalIdentification($number) : $this->validateNaturalPersonRuc($number);
    }

    /**
     * Validate that the number belongs to juridical persons.
     *
     * @param $number
     * @return string|null
     */
    public function validateIsJuridicalPersons($number)
    {
        return $this->validatePrivateCompanyRuc($number) !== null ?
            $this->validatePrivateCompanyRuc($number) : $this->validatePublicCompanyRuc($number);
    }

    /**
     * Validate the number with all types of documents.
     *
     * @param $number
     * @return string|null
     */
    public function validateAllIdentificatons($number)
    {
        $result = $this->validateFinalConsumer($number);

        if ($result) {
            return $result;
        }

        $result = $this->validatePersonalIdentification($number);

        if ($result) {
            return $result;
        }

        $result = $this->validateNaturalPersonRuc($number);

        if ($result) {
            return $result;
        }

        $result = $this->validatePrivateCompanyRuc($number);

        if ($result) {
            return $result;
        }

        $result = $this->validatePublicCompanyRuc($number);

        if ($result) {
            return $result;
        }

        return null;
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
    private function initValidation($value, $len)
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
    private function provinceCodeValidation($value)
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
     * @param string $type Type of identifier
     * @return boolean
     * @throws EcuadorIdentificationException When it does not comply with the validation according to the type of identifier
     */
    private function thirdDigitValidation($value, $type)
    {
        switch ($type) {
            case self::NaturalPerson:
                $min = config('laravel-ecuador-identification.personal-identification.third-digit.min');
                $max = config('laravel-ecuador-identification.personal-identification.third-digit.max');
                if ($value < $min || $value > $max)
                    throw new EcuadorIdentificationException("Field must have the third digit between {$min} and {$max}.");
                break;

            case self::PublicCompany:
                $thirdDigit = config('laravel-ecuador-identification.public-ruc.third-digit');
                if ($value != $thirdDigit)
                    throw new EcuadorIdentificationException("Field must have the third digit equal to {$thirdDigit}.");
                break;

            case self::PrivateCompany:
                $thirdDigit = config('laravel-ecuador-identification.private-ruc.third-digit');
                if ($value != $thirdDigit)
                    throw new EcuadorIdentificationException("Field must have the third digit equal to {$thirdDigit}.");
                break;

            default:
                throw new EcuadorIdentificationException('Field does not have this type of identification.');
        }

        return true;
    }

    /**
     * Validation of the last digits
     *
     * Public Ruc => 0001
     * Other Ruc => 001
     *
     * @param string $value The last digits
     * @param string $type Type of identifier
     * @return boolean
     * @throws EcuadorIdentificationException When not equal to 001
     */
    private function theLastDigitsValidation($value, $type)
    {
        switch ($type) {
            case self::NaturalPerson:
                $lastDigits = config('laravel-ecuador-identification.natural-ruc.last-digits');
                if ($value != $lastDigits) {
                    throw new EcuadorIdentificationException("Field does not have the last digits equal to {$lastDigits}");
                }
                break;

            case self::PrivateCompany:
                $lastDigits = config('laravel-ecuador-identification.private-ruc.last-digits');
                if ($value != $lastDigits) {
                    throw new EcuadorIdentificationException("Field does not have the last digits equal to {$lastDigits}");
                }
                break;

            case self::PublicCompany:
                $lastDigits = config('laravel-ecuador-identification.public-ruc.last-digits');
                if ($value != $lastDigits) {
                    throw new EcuadorIdentificationException("Field does not have the last digits equal to {$lastDigits}");
                }
                break;

            default:
                throw new EcuadorIdentificationException('Field does not have this type of identification.');
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
     * @param string $type Type of identifier
     * @return boolean
     * @throws EcuadorIdentificationException The verified digit does not match the verification digit.
     */
    protected function moduleEleven($number, $type)
    {
        switch ($type) {
            case self::PrivateCompany:
                $coefficients = config('laravel-ecuador-identification.private-ruc.coefficients');
                $check_digit_position = config('laravel-ecuador-identification.private-ruc.check-digit-position');
                $check_digit_value = $number[$check_digit_position - 1];
                $numbers = str_split(substr($number, 0, 9));
                break;
            case self::PublicCompany:
                $coefficients = config('laravel-ecuador-identification.public-ruc.coefficients');
                $check_digit_position = config('laravel-ecuador-identification.public-ruc.check-digit-position');
                $check_digit_value = $number[$check_digit_position - 1];
                $numbers = str_split(substr($number, 0, 8));
                break;
            default:
                throw new EcuadorIdentificationException('Field does not have this type of identification.');
                break;
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
