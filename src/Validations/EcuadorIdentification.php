<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Validations;

use Exception;
use BadMethodCallException;
use Illuminate\Support\Str;
use Luilliarcec\LaravelEcuadorIdentification\Support\Identifications\FinalCustomer;
use Luilliarcec\LaravelEcuadorIdentification\Support\Identifications\NaturalRuc;
use Luilliarcec\LaravelEcuadorIdentification\Support\Identifications\PersonalIdentification;
use Luilliarcec\LaravelEcuadorIdentification\Support\Identifications\PrivateRuc;
use Luilliarcec\LaravelEcuadorIdentification\Support\Identifications\PublicRuc;

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
     * Error encapsulator variable
     *
     * @var string
     */
    private $error;

    /**
     * Set Error
     *
     * @param string|null $error
     */
    protected function setError(?string $error): void
    {
        $this->error = $error;
    }

    /**
     * Get Error
     *
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Validate that the Ecuadorian identification is valid
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     * @return bool
     */
    public function validate($attribute, $value, $parameters, $validator)
    {
        $method = 'validate' . Str::studly($parameters[0]);

        try {
            return !is_null($this->{$method}($value));
        } catch (Exception $exception) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.', static::class, $parameters[0]
            ));
        }
    }

    /**
     * Validates the Ecuadorian Final Consumer
     *
     * @param string $identification_number Final Consumer Identification
     * @return string|null
     */
    public function validateFinalConsumer(string $identification_number)
    {
        $this->setError(null);

        try {
            return (new FinalCustomer())->validate($identification_number);
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return null;
        }
    }

    /**
     * Validates the Ecuadorian Identification Card
     *
     * @param string $identification_number Number of Identification Card
     * @return string|null
     */
    public function validatePersonalIdentification(string $identification_number)
    {
        $this->setError(null);

        try {
            return (new PersonalIdentification())->validate($identification_number);
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return null;
        }
    }

    /**
     * Validates the Ecuadorian RUC of Natural Person
     *
     * @param string $identification_number Number of RUC Natural Person
     * @return string|null
     */
    public function validateNaturalRuc(string $identification_number)
    {
        $this->setError(null);

        try {
            return (new NaturalRuc())->validate($identification_number);
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return null;
        }
    }

    /**
     * Validates the Ecuadorian RUC of Public Companies
     *
     * @param string $identification_number Number of RUC Public Companies
     * @return string|null
     */
    public function validatePublicRuc(string $identification_number)
    {
        $this->setError(null);

        try {
            return (new PublicRuc())->validate($identification_number);
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return null;
        }
    }

    /**
     * Validates the Ecuadorian RUC of Private Companies
     *
     * @param string $identification_number Number of RUC Private Companies
     * @return string|null
     */
    public function validatePrivateRuc(string $identification_number)
    {
        $this->setError(null);

        try {
            return (new PrivateRuc())->validate($identification_number);
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return null;
        }
    }

    /**
     * Validates the Ecuadorian Ruc's
     *
     * @param string $identification_number Number of RUC
     * @return string|null
     */
    public function validateRuc(string $identification_number)
    {
        if (($result = $this->validatePrivateRuc($identification_number)) !== null) {
            return $result;
        }

        if (($result = $this->validatePublicRuc($identification_number)) !== null) {
            return $result;
        }

        return $this->validateNaturalRuc($identification_number);
    }

    /**
     * Validate that the number belongs to natural persons.
     *
     * @param string $identification_number Number of identification
     * @return string|null
     */
    public function validateIsNaturalPerson(string $identification_number)
    {
        return $this->validatePersonalIdentification($identification_number) ?: $this->validateNaturalRuc($identification_number);
    }

    /**
     * Validate that the number belongs to juridical persons.
     *
     * @param string $identification_number Number of identification
     * @return string|null
     */
    public function validateIsJuridicalPerson(string $identification_number)
    {
        return $this->validatePrivateRuc($identification_number) ?: $this->validatePublicRuc($identification_number);
    }

    /**
     * Validate the number with all types of documents.
     *
     * @param string $identification_number Number of identification
     * @return string|null
     */
    public function validateAllIdentifications(string $identification_number)
    {
        if (($result = $this->validateFinalConsumer($identification_number)) !== null) {
            return $result;
        }

        if (($result = $this->validateRuc($identification_number)) !== null) {
            return $result;
        }

        return $this->validatePersonalIdentification($identification_number);
    }
}
