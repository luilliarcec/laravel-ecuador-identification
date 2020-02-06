<?php


namespace Luilliarcec\LaravelEcuadorIdentification\Tests\Unit;

use Luilliarcec\LaravelEcuadorIdentification\Facades\EcuadorIdentificationFacade;
use Luilliarcec\LaravelEcuadorIdentification\Support\EcuadorIdentification;
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

        $this->ecuadorIdentification = new EcuadorIdentification();
    }

    /** @test */
    public function validate_that_empty_values_are_not_allowed()
    {
        $this->assertNull($this->ecuadorIdentification->validatePrivateCompanyRuc(''));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Field must have a value.');
    }

    /** @test */
    public function validate_that_only_digits_are_allowed()
    {
        $this->assertNull($this->ecuadorIdentification->validatePrivateCompanyRuc('ABCDEFG'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Must be digits.');

        $this->assertNull($this->ecuadorIdentification->validatePrivateCompanyRuc('-0159623'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Must be digits.');
    }

    /** @test */
    public function validate_that_the_number_has_the_exact_length()
    {
        $len = $this->app->get('config')['laravel-ecuador-identification.type-identifications.ruc.length'];

        $this->assertNull($this->ecuadorIdentification->validatePrivateCompanyRuc('123456789012'));
        $this->assertEquals($this->ecuadorIdentification->getError(), "Must be {$len} digits.");

        $this->assertNull($this->ecuadorIdentification->validatePrivateCompanyRuc('12345678901234'));
        $this->assertEquals($this->ecuadorIdentification->getError(), "Must be {$len} digits.");
    }

    /** @test */
    public function validate_that_the_province_code_is_between_1_and_24()
    {
        $provinces = $this->app->get('config')['laravel-ecuador-identification.provinces'];

        $this->assertNull($this->ecuadorIdentification->validatePrivateCompanyRuc('0034567890123'));
        $this->assertEquals($this->ecuadorIdentification->getError(),
            "In your province code must be between 01 and {$provinces}.");

        $this->assertNull($this->ecuadorIdentification->validatePrivateCompanyRuc('2534567890123'));
        $this->assertEquals($this->ecuadorIdentification->getError(),
            "In your province code must be between 01 and {$provinces}.");

        $this->assertNull($this->ecuadorIdentification->validatePrivateCompanyRuc('2434567890123'));
        $this->assertNotEquals($this->ecuadorIdentification->getError(),
            "In your province code must be between 01 and {$provinces}.");

        $this->assertNull($this->ecuadorIdentification->validatePrivateCompanyRuc('0134567898123'));
        $this->assertNotEquals($this->ecuadorIdentification->getError(),
            "In your province code must be between 01 and {$provinces}.");
    }

    /** @test */
    public function validate_that_the_third_digit_is_9()
    {
        $thirdDigit = $this->app->get('config')['laravel-ecuador-identification.private-ruc.third-digit'];

        $this->assertNull($this->ecuadorIdentification->validatePrivateCompanyRuc('0154567890123'));
        $this->assertEquals($this->ecuadorIdentification->getError(),
            "Field must have the third digit equal to {$thirdDigit}.");

        $this->assertNull($this->ecuadorIdentification->validatePrivateCompanyRuc('0194567898123'));
        $this->assertNotEquals($this->ecuadorIdentification->getError(),
            "Field must have the third digit equal to {$thirdDigit}.");
    }

    /** @test */
    public function validate_that_the_last_digits_are_001()
    {
        $lastDigits = $this->app->get('config')['laravel-ecuador-identification.private-ruc.last-digits'];

        $this->assertNull($this->ecuadorIdentification->validatePrivateCompanyRuc('0194567890123'));
        $this->assertEquals($this->ecuadorIdentification->getError(),
            "Field does not have the last digits equal to {$lastDigits}");

        $this->assertNull($this->ecuadorIdentification->validatePrivateCompanyRuc('1790001568001'));
        $this->assertNotEquals($this->ecuadorIdentification->getError(),
            "Field does not have the last digits equal to {$lastDigits}");
    }

    /** @test */
    public function validate_that_the_certificate_is_valid()
    {
        $billingCode = $this->app->get('config')['laravel-ecuador-identification.type-identifications.ruc.billing-code'];

        $this->assertNull($this->ecuadorIdentification->validatePrivateCompanyRuc('1790022674001'));
        $this->assertEquals($this->ecuadorIdentification->getError(),
            'Field is invalid');

        $this->assertEquals($this->ecuadorIdentification->validatePrivateCompanyRuc('1790011674001'), $billingCode);

        $this->assertNull(EcuadorIdentificationFacade::validatePrivateCompanyRuc('1790022674001'));
        $this->assertEquals(EcuadorIdentificationFacade::getError(), 'Field is invalid');

        $this->assertEquals(EcuadorIdentificationFacade::validatePrivateCompanyRuc('1790011674001'), $billingCode);
    }
}
