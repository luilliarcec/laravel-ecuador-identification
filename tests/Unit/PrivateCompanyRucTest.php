<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Tests\Unit;

use Luilliarcec\LaravelEcuadorIdentification\Facades\EcuadorIdentification;
use Luilliarcec\LaravelEcuadorIdentification\Validations\EcuadorIdentification as Identification;
use Luilliarcec\LaravelEcuadorIdentification\Tests\TestCase;

class PrivateCompanyRucTest extends TestCase
{
    /**
     * @var EcuadorIdentification
     */
    protected $ecuadorIdentification;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->ecuadorIdentification = new Identification();
    }

    /** @test */
    public function validate_that_empty_values_are_not_allowed()
    {
        $this->assertNull($this->ecuadorIdentification->validatePrivateRuc(''));
        $this->assertEquals('Field must have a value.', $this->ecuadorIdentification->getError());
    }

    /** @test */
    public function validate_that_only_digits_are_allowed()
    {
        $this->assertNull($this->ecuadorIdentification->validatePrivateRuc('ABCDEFG'));
        $this->assertEquals('Field must be digits.', $this->ecuadorIdentification->getError());

        $this->assertNull($this->ecuadorIdentification->validatePrivateRuc('-0159623'));
        $this->assertEquals('Field must be digits.', $this->ecuadorIdentification->getError());
    }

    /** @test */
    public function validate_that_the_number_has_the_exact_length()
    {
        $this->assertNull($this->ecuadorIdentification->validatePrivateRuc('123456789012'));
        $this->assertEquals('Field must be 13 digits.', $this->ecuadorIdentification->getError());

        $this->assertNull($this->ecuadorIdentification->validatePrivateRuc('12345678901234'));
        $this->assertEquals('Field must be 13 digits.', $this->ecuadorIdentification->getError());
    }

    /** @test */
    public function validate_that_the_province_code_is_between_1_and_24()
    {
        $this->assertNull($this->ecuadorIdentification->validatePrivateRuc('0034567890123'));
        $this->assertEquals('In your province code must be between 01 and 24.', $this->ecuadorIdentification->getError());

        $this->assertNull($this->ecuadorIdentification->validatePrivateRuc('2534567890123'));
        $this->assertEquals('In your province code must be between 01 and 24.', $this->ecuadorIdentification->getError());

        $this->assertNull($this->ecuadorIdentification->validatePrivateRuc('2434567890123'));
        $this->assertNotEquals('In your province code must be between 01 and 24.', $this->ecuadorIdentification->getError());

        $this->assertNull($this->ecuadorIdentification->validatePrivateRuc('0134567898123'));
        $this->assertNotEquals('In your province code must be between 01 and 24.', $this->ecuadorIdentification->getError());
    }

    /** @test */
    public function validate_that_the_third_digit_is_9()
    {
        $this->assertNull($this->ecuadorIdentification->validatePrivateRuc('0154567890123'));
        $this->assertEquals("Field must have the third digit equal to 9.", $this->ecuadorIdentification->getError());

        $this->assertNull($this->ecuadorIdentification->validatePrivateRuc('0194567898123'));
        $this->assertNotEquals("Field must have the third digit equal to 9.", $this->ecuadorIdentification->getError());
    }

    /** @test */
    public function validate_that_the_last_digits_are_001()
    {
        $this->assertNull($this->ecuadorIdentification->validatePrivateRuc('0194567890123'));
        $this->assertEquals('Field does not have the last digits equal to 001.', $this->ecuadorIdentification->getError());

        $this->assertNull($this->ecuadorIdentification->validatePrivateRuc('1790001568001'));
        $this->assertNotEquals('Field does not have the last digits equal to 001.', $this->ecuadorIdentification->getError());
    }

    /** @test */
    public function validate_that_the_certificate_is_valid()
    {
        $this->assertNull($this->ecuadorIdentification->validatePrivateRuc('1790022674001'));
        $this->assertEquals('The identification number is invalid.', $this->ecuadorIdentification->getError());

        $this->assertEquals('04', $this->ecuadorIdentification->validatePrivateRuc('1790011674001'));

        $this->assertNull(EcuadorIdentification::validatePrivateRuc('1790022674001'));
        $this->assertEquals('The identification number is invalid.', EcuadorIdentification::getError());

        $this->assertEquals('04', EcuadorIdentification::validatePrivateRuc('1790011674001'));
    }

    /** @test */
    public function validate_integration_with_validator_laravel_and_response_error_message()
    {
        $data = array('identification' => '1760001550001');
        $rules = array('identification' => 'ecuador:private_ruc');

        $validator = $this->app['validator']->make($data, $rules);

        $this->assertEquals('The identification field is invalid.',
            $validator->getMessageBag()->get('identification')[0]);
    }

    /** @test */
    public function validate_integration_with_validator_laravel_and_response_success()
    {
        $data = array('identification' => '1790011674001');
        $rules = array('identification' => 'ecuador:private_ruc');

        $validator = $this->app['validator']->make($data, $rules);

        $this->assertFalse($validator->fails());
    }
}
