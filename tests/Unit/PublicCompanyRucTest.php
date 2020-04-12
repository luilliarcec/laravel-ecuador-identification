<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Tests\Unit;

use Luilliarcec\LaravelEcuadorIdentification\Facades\EcuadorIdentification;
use Luilliarcec\LaravelEcuadorIdentification\Support\EcuadorIdentification as Identification;
use Luilliarcec\LaravelEcuadorIdentification\Tests\TestCase;

class PublicCompanyRucTest extends TestCase
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
        $this->assertNull($this->ecuadorIdentification->validatePublicRuc(''));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Field must have a value.');
    }

    /** @test */
    public function validate_that_only_digits_are_allowed()
    {
        $this->assertNull($this->ecuadorIdentification->validatePublicRuc('ABCDEFG'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Field must be digits.');

        $this->assertNull($this->ecuadorIdentification->validatePublicRuc('-0159623'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Field must be digits.');
    }

    /** @test */
    public function validate_that_the_number_has_the_exact_length()
    {
        $this->assertNull($this->ecuadorIdentification->validatePublicRuc('123456789012'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Field must be 13 digits.');

        $this->assertNull($this->ecuadorIdentification->validatePublicRuc('12345678901234'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Field must be 13 digits.');
    }

    /** @test */
    public function validate_that_the_province_code_is_between_1_and_24()
    {
        $this->assertNull($this->ecuadorIdentification->validatePublicRuc('0034567890123'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'In your province code must be between 01 and 24.');

        $this->assertNull($this->ecuadorIdentification->validatePublicRuc('2534567890123'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'In your province code must be between 01 and 24.');

        $this->assertNull($this->ecuadorIdentification->validatePublicRuc('2434567890123'));
        $this->assertNotEquals($this->ecuadorIdentification->getError(), 'In your province code must be between 01 and 24.');

        $this->assertNull($this->ecuadorIdentification->validatePublicRuc('0134567898123'));
        $this->assertNotEquals($this->ecuadorIdentification->getError(), 'In your province code must be between 01 and 24.');
    }

    /** @test */
    public function validate_that_the_third_digit_is_between_0_and_5()
    {
        $this->assertNull($this->ecuadorIdentification->validatePublicRuc('0154567890123'));
        $this->assertEquals($this->ecuadorIdentification->getError(), "Field must have the third digit equal to 6.");

        $this->assertNull($this->ecuadorIdentification->validatePublicRuc('0164567898123'));
        $this->assertNotEquals($this->ecuadorIdentification->getError(), "Field must have the third digit equal to 6.");
    }

    /** @test */
    public function validate_that_the_last_digits_are_0001()
    {
        $this->assertNull($this->ecuadorIdentification->validatePublicRuc('0164567890123'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Field does not have the last digits equal to 0001.');

        $this->assertNull($this->ecuadorIdentification->validatePublicRuc('1760001560001'));
        $this->assertNotEquals($this->ecuadorIdentification->getError(), 'Field does not have the last digits equal to 0001.');
    }

    /** @test */
    public function validate_that_the_certificate_is_valid()
    {
        $this->assertNull($this->ecuadorIdentification->validatePublicRuc('0164567890001'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'The identification number is invalid.');

        $this->assertEquals($this->ecuadorIdentification->validatePublicRuc('1760001550001'), '04');

        $this->assertNull(EcuadorIdentification::validatePublicRuc('0164567890001'));
        $this->assertEquals(EcuadorIdentification::getError(), 'The identification number is invalid.');

        $this->assertEquals(EcuadorIdentification::validatePublicRuc('1760001550001'), '04');
    }
}
