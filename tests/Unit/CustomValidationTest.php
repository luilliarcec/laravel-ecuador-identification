<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Tests\Unit;

use Luilliarcec\LaravelEcuadorIdentification\Validations\EcuadorIdentification;
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
        $this->assertEquals('05', $this->ecuadorIdentification->validateAllIdentifications('1710034065')); // Personal Identification
        $this->assertEquals('04', $this->ecuadorIdentification->validateAllIdentifications('1710034065001')); // Natural RUC
        $this->assertEquals('04', $this->ecuadorIdentification->validateAllIdentifications('1790011674001')); // Private RUC
        $this->assertEquals('04', $this->ecuadorIdentification->validateAllIdentifications('1760001550001')); // Public RUC
        $this->assertEquals('07', $this->ecuadorIdentification->validateAllIdentifications('9999999999999')); // Final Customer
        $this->assertNull($this->ecuadorIdentification->validateAllIdentifications('9999999999998'));
    }

    /** @test */
    public function validate_is_juridical_person()
    {
        $this->assertNull($this->ecuadorIdentification->validateIsJuridicalPerson('1710034065'));
        $this->assertNull($this->ecuadorIdentification->validateIsJuridicalPerson('9999999999999'));
        $this->assertNull($this->ecuadorIdentification->validateIsJuridicalPerson('1710034065001'));

        $this->assertEquals('04', $this->ecuadorIdentification->validateIsJuridicalPerson('1790011674001'));
        $this->assertEquals('04', $this->ecuadorIdentification->validateIsJuridicalPerson('1760001550001'));
    }

    /** @test */
    public function validate_is_natural_person()
    {
        $this->assertNull($this->ecuadorIdentification->validateIsNaturalPerson('1790011674001'));
        $this->assertNull($this->ecuadorIdentification->validateIsNaturalPerson('1760001550001'));
        $this->assertNull($this->ecuadorIdentification->validateIsNaturalPerson('9999999999999'));

        $this->assertEquals('05', $this->ecuadorIdentification->validateIsNaturalPerson('1710034065'));
        $this->assertEquals('04', $this->ecuadorIdentification->validateIsNaturalPerson('1710034065001'));
    }

    /** @test */
    public function validate_rucs()
    {
        $this->assertNull($this->ecuadorIdentification->validateRuc('1710034065'));
        $this->assertNull($this->ecuadorIdentification->validateRuc('1710034065002'));
        $this->assertNull($this->ecuadorIdentification->validateRuc('9999999999999'));
        $this->assertNull($this->ecuadorIdentification->validateRuc('1760001520001'));
        $this->assertNull($this->ecuadorIdentification->validateRuc('1790011274001'));

        $this->assertEquals('04', $this->ecuadorIdentification->validateRuc('1710034065001')); // Natural
        $this->assertEquals('04', $this->ecuadorIdentification->validateRuc('1760001550001')); // Public
        $this->assertEquals('04', $this->ecuadorIdentification->validateRuc('1790011674001')); // Private
    }

    /** @test */
    public function validate_integration_with_validator_laravel_and_response_error_message()
    {
        $data = array('identification' => '1710034065002');
        $rules = array('identification' => 'ecuador:natural_ruc');

        $validator = $this->app['validator']->make($data, $rules);

        $this->assertEquals('The identification field is invalid.',
            $validator->getMessageBag()->get('identification')[0]);
    }

    /** @test */
    public function validate_integration_with_validator_laravel_and_response_success()
    {
        $data = array('identification' => '1710034065001');
        $rules = array('identification' => 'ecuador:natural_ruc');

        $validator = $this->app['validator']->make($data, $rules);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function check_that_validation_does_not_run_on_null()
    {
        $data = array('identification' => null);
        $rules = array('identification' => 'ecuador:natural_ruc');

        $validator = $this->app['validator']->make($data, $rules);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function check_that_validation_does_not_run_when_nullable()
    {
        $data = array('identification' => null);
        $rules = array('identification' => 'nullable|ecuador:natural_ruc');

        $validator = $this->app['validator']->make($data, $rules);

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function check_that_it_runs_when_required_and_null()
    {
        $data = array('identification' => null);
        $rules = array('identification' => 'required|ecuador:natural_ruc');

        $validator = $this->app['validator']->make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertEquals('The identification field is required.',
            $validator->getMessageBag()->get('identification')[0]
        );
    }

    /** @test */
    public function check_that_it_runs_when_required_and_not_null()
    {
        $data = array('identification' => '09');
        $rules = array('identification' => 'required|ecuador:natural_ruc');

        $validator = $this->app['validator']->make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertEquals('The identification field is invalid.',
            $validator->getMessageBag()->get('identification')[0]
        );
    }
}
