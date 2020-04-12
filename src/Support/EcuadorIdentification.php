<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Support;

use Luilliarcec\LaravelEcuadorIdentification\Exceptions\EcuadorIdentificationException;
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
        $identification = new PersonalIdentification();

        try {
            return $identification->validate($number);
        } catch (EcuadorIdentificationException $e) {
            $this->setError($e->getMessage());
            return null;
        }
    }

    /**
     * Validates the Ecuadorian RUC of Natural Person
     *
     * @param string $number Number of RUC Natural Person
     * @return string|null
     */
    public function validateNaturalPersonRuc($number)
    {
        $identification = new NaturalRuc();

        try {
            return $identification->validate($number);
        } catch (EcuadorIdentificationException $e) {
            $this->setError($e->getMessage());
            return null;
        }
    }

    /**
     * Validates the Ecuadorian RUC of Private Companies
     *
     * @param string $number Number of RUC Private Companies
     * @return string|null
     */
    public function validatePrivateCompanyRuc($number)
    {
        $identification = new PrivateRuc();

        try {
            return $identification->validate($number);
        } catch (EcuadorIdentificationException $e) {
            $this->setError($e->getMessage());
            return null;
        }
    }

    /**
     * Validates the Ecuadorian RUC of Public Companies
     *
     * @param string $number Number of RUC Public Companies
     * @return string|null
     */
    public function validatePublicCompanyRuc($number)
    {
        $identification = new PublicRuc();

        try {
            return $identification->validate($number);
        } catch (EcuadorIdentificationException $e) {
            $this->setError($e->getMessage());
            return null;
        }
    }

    /**
     * Validates the Ecuadorian Final Consumer
     *
     * @param $number
     * @return string|null
     */
    public function validateFinalConsumer($number)
    {
        $identification = new FinalCustomer();

        try {
            return $identification->validate($number);
        } catch (EcuadorIdentificationException $e) {
            $this->setError($e->getMessage());
            return null;
        }
    }

    /**
     * Validates the Ecuadorian Ruc's
     *
     * @param $number
     * @return string|null
     */
    public function validateRuc($number)
    {
        if (($result = $this->validatePrivateCompanyRuc($number)) !== null) {
            return $result;
        }

        if (($result = $this->validatePublicCompanyRuc($number)) !== null) {
            return $result;
        }

        return $this->validateNaturalPersonRuc($number);
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
        if (($result = $this->validateFinalConsumer($number)) !== null) {
            return $result;
        }

        if (($result = $this->validatePersonalIdentification($number)) !== null) {
            return $result;
        }

        if (($result = $this->validateNaturalPersonRuc($number)) !== null) {
            return $result;
        }

        if (($result = $this->validatePrivateCompanyRuc($number)) !== null) {
            return $result;
        }

        return $this->validatePublicCompanyRuc($number);
    }
}
