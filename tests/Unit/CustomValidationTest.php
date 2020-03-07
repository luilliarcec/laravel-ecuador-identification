<?php


namespace Luilliarcec\LaravelEcuadorIdentification\Tests\Unit;


use Luilliarcec\LaravelEcuadorIdentification\Support\EcuadorIdentification;
use Luilliarcec\LaravelEcuadorIdentification\Tests\TestCase;

class CustomValidationTest extends TestCase
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
    public function validate_that_all_type_document_return_correct_value()
    {
        $this->assertEquals($this->ecuadorIdentification->validateAllIdentificatons('1710034065'), // id
            $this->app->get('config')['laravel-ecuador-identification.type-identifications.personal-identification.billing-code']);

        $this->assertEquals($this->ecuadorIdentification->validateAllIdentificatons('1710034065001'), // natural ruc
            $this->app->get('config')['laravel-ecuador-identification.type-identifications.ruc.billing-code']);

        $this->assertEquals($this->ecuadorIdentification->validateAllIdentificatons('1790011674001'), // private ruc
            $this->app->get('config')['laravel-ecuador-identification.type-identifications.ruc.billing-code']);

        $this->assertEquals($this->ecuadorIdentification->validateAllIdentificatons('1760001550001'), // public ruc
            $this->app->get('config')['laravel-ecuador-identification.type-identifications.ruc.billing-code']);

        $this->assertEquals($this->ecuadorIdentification->validateAllIdentificatons('9999999999999'), // final customer
            $this->app->get('config')['laravel-ecuador-identification.type-identifications.final-customer.billing-code']);

        $this->assertNull($this->ecuadorIdentification->validateAllIdentificatons('9999999999998'));
    }

    /** @test */
    public function validate_is_juridical_person()
    {
        $this->assertNull($this->ecuadorIdentification->validateIsJuridicalPersons('1710034065'));
        $this->assertNull($this->ecuadorIdentification->validateIsJuridicalPersons('9999999999999'));
        $this->assertNull($this->ecuadorIdentification->validateIsJuridicalPersons('1710034065001'));

        $this->assertEquals($this->ecuadorIdentification->validateIsJuridicalPersons('1790011674001'),
            $this->app->get('config')['laravel-ecuador-identification.type-identifications.ruc.billing-code']);

        $this->assertEquals($this->ecuadorIdentification->validateIsJuridicalPersons('1760001550001'),
            $this->app->get('config')['laravel-ecuador-identification.type-identifications.ruc.billing-code']);
    }

    /** @test */
    public function validate_is_natural_person()
    {
        $this->assertNull($this->ecuadorIdentification->validateIsNaturalPersons('1790011674001'));
        $this->assertNull($this->ecuadorIdentification->validateIsNaturalPersons('1760001550001'));
        $this->assertNull($this->ecuadorIdentification->validateIsNaturalPersons('9999999999999'));

        $this->assertEquals($this->ecuadorIdentification->validateIsNaturalPersons('1710034065'),
            $this->app->get('config')['laravel-ecuador-identification.type-identifications.personal-identification.billing-code']);

        $this->assertEquals($this->ecuadorIdentification->validateIsNaturalPersons('1710034065001'),
            $this->app->get('config')['laravel-ecuador-identification.type-identifications.ruc.billing-code']);
    }

    /** @test */
    public function validate_rucs()
    {
        $this->assertNull($this->ecuadorIdentification->validateRuc('1710034065'));
        $this->assertNull($this->ecuadorIdentification->validateRuc('1710034065002'));
        $this->assertNull($this->ecuadorIdentification->validateRuc('9999999999999'));
        $this->assertNull($this->ecuadorIdentification->validateRuc('1760001520001'));
        $this->assertNull($this->ecuadorIdentification->validateRuc('1790011274001'));

        $this->assertEquals($this->ecuadorIdentification->validateRuc('1710034065001'),
            $this->app->get('config')['laravel-ecuador-identification.type-identifications.ruc.billing-code']); // Natural

        $this->assertEquals($this->ecuadorIdentification->validateRuc('1760001550001'),
            $this->app->get('config')['laravel-ecuador-identification.type-identifications.ruc.billing-code']); // Public

        $this->assertEquals($this->ecuadorIdentification->validateRuc('1790011674001'),
            $this->app->get('config')['laravel-ecuador-identification.type-identifications.ruc.billing-code']); // Private
    }

    /** @test */
    public function validate_integration_with_validator_laravel_and_response_error_message()
    {
        $data = [
            'identification' => '1710034065002'
        ];

        $rules = [
            'identification' => 'ecuador:natural_ruc'
        ];

        $validator = $this->app['validator']->make($data, $rules);

        $this->assertEquals($validator->getMessageBag()->get('identification')[0],
            'The identification field does not have the corresponding country format. (Ecuador)');
    }
}
