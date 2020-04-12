<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Tests\Units;

use Luilliarcec\LaravelEcuadorIdentification\Facades\EcuadorIdentification;
use Luilliarcec\LaravelEcuadorIdentification\Support\EcuadorIdentification as Identification;
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
        $this->ecuadorIdentification = new Identification();
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
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Field must be digits.');

        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('-0159623'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Field must be digits.');
    }

    /** @test */
    public function validate_that_the_number_has_the_exact_length()
    {
        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('123456789'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Field must be 10 digits.');

        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('12345678901'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Field must be 10 digits.');
    }

    /** @test */
    public function validate_that_the_province_code_is_between_1_and_24()
    {
        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('0034567890'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'In your province code must be between 01 and 24.');

        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('2534567890'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'In your province code must be between 01 and 24.');

        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('2434567898'));
        $this->assertNotEquals($this->ecuadorIdentification->getError(), 'In your province code must be between 01 and 24.');

        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('0134567898'));
        $this->assertNotEquals($this->ecuadorIdentification->getError(), 'In your province code must be between 01 and 24.');
    }

    /** @test */
    public function validate_that_the_third_digit_is_between_0_and_5()
    {
        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('0164567890'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Field must have the third digit less than or equal to 5.');

        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('0134567898'));
        $this->assertNotEquals($this->ecuadorIdentification->getError(), 'Field must have the third digit less than or equal to 5.');
    }

    /** @test */
    public function validate_that_the_certificate_is_valid()
    {
        $this->assertNull($this->ecuadorIdentification->validatePersonalIdentification('0154567890'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'The identification number is invalid.');

        $this->assertEquals($this->ecuadorIdentification->validatePersonalIdentification('0134567890'), '05');
        $this->assertEquals($this->ecuadorIdentification->validatePersonalIdentification('1710034065'), '05');

        $this->assertNull(EcuadorIdentification::validatePersonalIdentification('0154567890'));
        $this->assertEquals(EcuadorIdentification::getError(), 'The identification number is invalid.');

        $this->assertEquals(EcuadorIdentification::validatePersonalIdentification('1710034065'), '05');
    }
}
