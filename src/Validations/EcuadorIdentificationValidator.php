<?php


namespace Luilliarcec\LaravelEcuadorIdentification\Validations;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Luilliarcec\LaravelEcuadorIdentification\Support\EcuadorIdentification;

class EcuadorIdentificationValidator extends Validator
{
    /**
     * Validation types
     *
     * @var array
     */
    private $types = [
        'final_customer' => 'validateFinalConsumer',
        'personal_identification' => 'validatePersonalIdentification',
        'natural_ruc' => 'validateNaturalRuc',
        'public_ruc' => 'validatePublicRuc',
        'private_ruc' => 'validatePrivateRuc',
        'ruc' => 'validateRuc',
        'is_natural_person' => 'validateIsNaturalPersons',
        'is_juridical_person' => 'validateIsJuridicalPersons',
        'all_identifications' => 'validateAllTypeIdentification',
    ];

    /**
     * Validate that the Ecuadorian identification is valid
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     * @throws Exception
     */
    public function validateEcuador($attribute, $value, $parameters)
    {
        $lowerRule = explode(':', strtolower($this->currentRule))[0];

        try {
            if ($this->passesEcuador($parameters, $value)) {
                return true;
            }

            $this->setCustomMessages([$lowerRule => $this->getMessageEcuador($attribute, $lowerRule)]);
            return false;

        } catch (Exception $exception) {
            throw new ValidationException($this);
        }
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

    /**
     * Determine if the data passes the validation rules.
     *
     * @param $parameters
     * @param $value
     * @return bool
     */
    protected function passesEcuador($parameters, $value)
    {
        $validator = new EcuadorIdentification();

        return $validator->{$this->types[$parameters[0]]}($value) == null;
    }
}
