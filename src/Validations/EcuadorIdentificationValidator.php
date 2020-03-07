<?php


namespace Luilliarcec\LaravelEcuadorIdentification\Validations;

use Illuminate\Validation\Validator;
use Luilliarcec\LaravelEcuadorIdentification\Exceptions\EcuadorIdentificationException;
use Luilliarcec\LaravelEcuadorIdentification\Support\EcuadorIdentification;

class EcuadorIdentificationValidator extends Validator
{
    /**
     * Determine if the value is valid
     *
     * @var bool
     */
    private $isValid = false;

    /**
     * Validation types
     *
     * @var array
     */
    private $types = [
        'personal_identification' => 'validatePersonalIdentification',
        'all_identifications' => 'validateAllIdentificatons',
        'natural_ruc' => 'validateNaturalPersonRuc',
        'public_ruc' => 'validatePublicCompanyRuc',
        'private_ruc' => 'validatePrivateCompanyRuc',
        'is_juridical_person' => 'validateIsJuridicalPersons',
        'is_natural_person' => 'validateIsNaturalPersons',
    ];

    /**
     * Validate that the Ecuadorian identification is valid
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     * @throws EcuadorIdentificationException
     */
    public function validateEcuadorIdentification($attribute, $value, $parameters)
    {
        $validator = new EcuadorIdentification();

        $lowerRule = explode(':', strtolower($this->currentRule))[0];

        try {
            $this->isValid = $validator->{$this->types[$parameters[0]]}($value) == null ? false : true;
        } catch (\Exception $exception) {
            throw new EcuadorIdentificationException("Custom validation rule {$lowerRule}:{$parameters[0]} does not exist");
        }

        if (!$this->isValid) {
            $this->setCustomMessages([$lowerRule => $this->getMessageEcuador($attribute, $lowerRule)]);
            return $this->isValid;
        }

        return $this->isValid;
    }

    /**
     * Get the translated message or the default message.
     *
     * @param $attribute
     * @param $rule
     * @return mixed|string
     */
    protected function getMessageEcuador($attribute, $rule)
    {
        $key = 'validation.' . $rule;

        return $this->getTranslator()->get($key) != $key ?
            $this->getTranslator()->get($key) :
            "The {$this->getAttributeEcuador($attribute)} field does not have the corresponding country format. (Ecuador)";
    }

    /**
     * Get the translated attribute or the default attribute.
     *
     * @param $attribute
     * @return mixed
     */
    protected function getAttributeEcuador($attribute)
    {
        $key = 'validation.attributes.' . $attribute;

        return $this->getTranslator()->get($key) != $key ?
            $this->getTranslator()->get($key) :
            $attribute;
    }
}
