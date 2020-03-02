<?php


namespace Luilliarcec\LaravelEcuadorIdentification\Tests\Unit;


use Luilliarcec\LaravelEcuadorIdentification\Facades\EcuadorIdentification;
use Luilliarcec\LaravelEcuadorIdentification\Support\EcuadorIdentification as Identification;
use Luilliarcec\LaravelEcuadorIdentification\Tests\TestCase;

class NaturalPersonRucTest extends TestCase
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
        $this->assertNull($this->ecuadorIdentification->validateNaturalPersonRuc(''));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Field must have a value.');
    }

    /** @test */
    public
    function validate_that_only_digits_are_allowed()
    {
        $this->assertNull($this->ecuadorIdentification->validateNaturalPersonRuc('ABCDEFG'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Must be digits.');

        $this->assertNull($this->ecuadorIdentification->validateNaturalPersonRuc('-0159623'));
        $this->assertEquals($this->ecuadorIdentification->getError(), 'Must be digits.');
    }

    /** @test */
    public
    function validate_that_the_number_has_the_exact_length()
    {
        $len = $this->app->get('config')['laravel-ecuador-identification.type-identifications.ruc.length'];

        $this->assertNull($this->ecuadorIdentification->validateNaturalPersonRuc('123456789012'));
        $this->assertEquals($this->ecuadorIdentification->getError(), "Must be {$len} digits.");

        $this->assertNull($this->ecuadorIdentification->validateNaturalPersonRuc('12345678901234'));
        $this->assertEquals($this->ecuadorIdentification->getError(), "Must be {$len} digits.");
    }

    /** @test */
    public
    function validate_that_the_province_code_is_between_1_and_24()
    {
        $provinces = $this->app->get('config')['laravel-ecuador-identification.provinces'];

        $this->assertNull($this->ecuadorIdentification->validateNaturalPersonRuc('0034567890123'));
        $this->assertEquals($this->ecuadorIdentification->getError(),
            "In your province code must be between 01 and {$provinces}.");

        $this->assertNull($this->ecuadorIdentification->validateNaturalPersonRuc('2534567890123'));
        $this->assertEquals($this->ecuadorIdentification->getError(),
            "In your province code must be between 01 and {$provinces}.");

        $this->assertNull($this->ecuadorIdentification->validateNaturalPersonRuc('2494567890123'));
        $this->assertNotEquals($this->ecuadorIdentification->getError(),
            "In your province code must be between 01 and {$provinces}.");

        $this->assertNull($this->ecuadorIdentification->validateNaturalPersonRuc('0194567898123'));
        $this->assertNotEquals($this->ecuadorIdentification->getError(),
            "In your province code must be between 01 and {$provinces}.");
    }

    /** @test */
    public function validate_that_the_third_digit_is_between_0_and_5()
    {
        $min = $this->app->get('config')['laravel-ecuador-identification.personal-identification.third-digit.min'];
        $max = $this->app->get('config')['laravel-ecuador-identification.personal-identification.third-digit.max'];

        $this->assertNull($this->ecuadorIdentification->validateNaturalPersonRuc('0164567890123'));
        $this->assertEquals($this->ecuadorIdentification->getError(),
            "Field must have the third digit between {$min} and {$max}.");

        $this->assertNull($this->ecuadorIdentification->validateNaturalPersonRuc('0134567898123'));
        $this->assertNotEquals($this->ecuadorIdentification->getError(),
            "Field must have the third digit between {$min} and {$max}.");
    }

    /** @test */
    public function validate_that_the_last_digits_are_001()
    {
        $lastDigits = $this->app->get('config')['laravel-ecuador-identification.natural-ruc.last-digits'];

        $this->assertNull($this->ecuadorIdentification->validateNaturalPersonRuc('0154567890123'));
        $this->assertEquals($this->ecuadorIdentification->getError(),
            "Field does not have the last digits equal to {$lastDigits}");

        $this->assertNull($this->ecuadorIdentification->validateNaturalPersonRuc('0134567898001'));
        $this->assertNotEquals($this->ecuadorIdentification->getError(),
            "Field does not have the last digits equal to {$lastDigits}");
    }

    /** @test */
    public function validate_that_the_certificate_is_valid()
    {
        $billingCode = $this->app->get('config')['laravel-ecuador-identification.type-identifications.ruc.billing-code'];

        $this->assertNull($this->ecuadorIdentification->validateNaturalPersonRuc('0154567890001'));
        $this->assertEquals($this->ecuadorIdentification->getError(),
            'Field is invalid');

        $this->assertEquals($this->ecuadorIdentification->validateNaturalPersonRuc('0134567890001'), $billingCode);
        $this->assertEquals($this->ecuadorIdentification->validateNaturalPersonRuc('1710034065001'), $billingCode);

        $this->assertNull(EcuadorIdentification::validateNaturalPersonRuc('0154567890001'));
        $this->assertEquals(EcuadorIdentification::validateNaturalPersonRuc('0134567890001'), $billingCode);
    }
}
