<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Tests\Units;

use Luilliarcec\LaravelEcuadorIdentification\Facades\EcuadorIdentificationFacade;
use Luilliarcec\LaravelEcuadorIdentification\Support\EcuadorIdentification;
use Luilliarcec\LaravelEcuadorIdentification\Tests\TestCase;

class PersonalIdentificationTest extends TestCase
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

        $this->ecuadorIdentification = new EcuadorIdentification();
    }

    /** @test */
    public function validate_that_empty_values_are_not_allowed()
    {
        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification(''));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Field must have a value.');
    }

    /** @test */
    public function validate_that_only_digits_are_allowed()
    {
        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('ABCDEFG'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Must be digits.');

        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('-0159623'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Must be digits.');
    }

    /** @test */
    public function validate_that_the_number_has_the_exact_length()
    {
        $len = $this->app->get('config')['laravel-ecuador-identification.type-identifications.personal-identification.length'];

        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('123456789'));
        $this->assertEquals($this->ecuadorIdentification->getError(), "Must be {$len} digits.");

        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('12345678901'));
        $this->assertEquals($this->ecuadorIdentification->getError(), "Must be {$len} digits.");
    }

    /** @test */
    public function validate_that_the_province_code_is_between_1_and_24()
    {
        $provinces = $this->app->get('config')['laravel-ecuador-identification.provinces'];

        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('0034567890'));
        $this->assertEquals($this->ecuadorIdentification->getError(),
            "In your province code must be between 01 and {$provinces}.");

        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('2534567890'));
        $this->assertEquals($this->ecuadorIdentification->getError(),
            "In your province code must be between 01 and {$provinces}.");

        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('2434567898'));
        $this->assertNotEquals($this->ecuadorIdentification->getError(),
            "In your province code must be between 01 and {$provinces}.");

        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('0134567898'));
        $this->assertNotEquals($this->ecuadorIdentification->getError(),
            "In your province code must be between 01 and {$provinces}.");
    }

    /** @test */
    public function validate_that_the_third_digit_is_between_0_and_5()
    {
        $min = $this->app->get('config')['laravel-ecuador-identification.personal-identification.third-digit.min'];
        $max = $this->app->get('config')['laravel-ecuador-identification.personal-identification.third-digit.max'];

        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('0164567890'));
        $this->assertEquals($this->ecuadorIdentification->getError(),
            "Field must have the third digit between {$min} and {$max}.");

        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('0134567898'));
        $this->assertNotEquals($this->ecuadorIdentification->getError(),
            "Field must have the third digit between {$min} and {$max}.");
    }

    /** @test */
    public function validate_that_the_certificate_is_valid()
    {
        $billingCode = $this->app->get('config')['laravel-ecuador-identification.type-identifications.personal-identification.billing-code'];

        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('0154567890'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Field is invalid');

        $this->assertEquals($this->ecuadorIdentification->validatePersonalIdentification('0134567890'), $billingCode);
        $this->assertEquals($this->ecuadorIdentification->validatePersonalIdentification('1710034065'), $billingCode);

        $this->assertNull(EcuadorIdentificationFacade::validatePersonalIdentification('0154567890'));
        $this->assertEquals(EcuadorIdentificationFacade::getError(), 'Field is invalid');
        
        $this->assertEquals(EcuadorIdentificationFacade::validatePersonalIdentification('1710034065'), $billingCode);
    }
}
