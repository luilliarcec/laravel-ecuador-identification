# Laravel Ecuador Identification

[![Latest Version on Packagist](https://img.shields.io/packagist/v/luilliarcec/laravel-ecuador-identification.svg)](https://packagist.org/packages/luilliarcec/laravel-ecuador-identification)
[![Build Status](https://img.shields.io/travis/luilliarcec/laravel-ecuador-identification/master.svg)](https://travis-ci.org/luilliarcec/laravel-ecuador-identification)
[![Quality Score](https://img.shields.io/scrutinizer/g/luilliarcec/laravel-ecuador-identification)](https://scrutinizer-ci.com/g/luilliarcec/laravel-ecuador-identification)
[![Total Downloads](https://img.shields.io/packagist/dt/luilliarcec/laravel-ecuador-identification)](https://packagist.org/packages/luilliarcec/laravel-ecuador-identification)
[![GitHub license](https://img.shields.io/github/license/luilliarcec/laravel-ecuador-identification.svg)](https://github.com/luilliarcec/laravel-ecuador-identification/blob/master/LICENSE.md)

Laravel Ecuador Identification is a validation library for Laravel, which allows the validation of personal and business identification documents, according to the country's tax regulations.

It is fully adaptable to Laravel's Facade and Validator Class. Its use is also shown in a Facade identified with the name of the country. You can use it as follows.

```php
$request->validate([
    'identification' => 'ecuador:natural_ruc',
]);
```

Or with Validator Facade

```php
$validator = Validator::make($request->all(), [
    'identification' => 'ecuador:natural_ruc',
]);
```

Or with Ecuador Facade

```php
use Luilliarcec\LaravelEcuadorIdentification\Facades\EcuadorIdentification;

EcuadorIdentification::validateNaturalPersonRuc('1710034065001'); // Return null or string code
```

## Installation

You can install the package via composer:

```bash
composer require luilliarcec/laravel-ecuador-identification
```

## Usage

When using the Laravel validator, each of them can be accessed by simply calling the validations by placing the [rule_name]:[validation_name].

### Ecuador (ecuador)

For ecuador use the "ecuador" rule

Ecuador has 5 types of documents, identification person or identity card, ruc of natural persons, ruc of private companies and ruc of public companies, in addition to billing the fictitious document of final consumer is used.

Validation rules:
* [Personal Identification](#rule-personal_identification)
* [Natural Person Ruc](#rule-natural_ruc)
* [Private Company Ruc](#rule-private_ruc)
* [Public Company Ruc](#rule-public_ruc)
* [Ruc](#rule-ruc)
* [All Identifications Validations](#rule-all_identifications)
* [Is Juridical Person](#rule-is_juridical_person)
* [Is Natural Person](#rule-is_natural_person)

<a name="rule-personal_identification"></a>
#### personal_identification
Validate the Ecuadorian identification card, this validation on the Facade returns your billing code

<a name="rule-natural_ruc"></a>
#### natural_ruc
Validates the Ecuadorian RUC of Natural Person, this validation on the Facade returns your billing code

<a name="rule-private_ruc"></a>
#### private_ruc
Validates the Ecuadorian RUC of Private Companies, this validation on the Facade returns your billing code

<a name="rule-public_ruc"></a>
#### public_ruc
Validates the Ecuadorian RUC of Public Companies, this validation on the Facade returns your billing code

<a name="rule-ruc"></a>
#### ruc
Validates the Ecuadorian RUC Companies (Public, Natural and Private), this validation on the Facade returns your billing code

<a name="rule-all_identifications"></a>
#### all_identifications
Validate the number with all types of documents. It includes the validation of final consumer. This validation in the Facade returns the corresponding billing code, if it fails, it returns null.

<a name="rule-is_juridical_person"></a>
#### is_juridical_person
The group called juridical persons are those that have an private ruc or a public ruc such validation on the Facade will return the billing code if the person has one of these documents, otherwise null.

<a name="rule-is_natural_person"></a>
#### is_natural_person
The group called natural persons are those that have an Ecuadorian identity card or a natural ruc such validation on the Facade will return the billing code if the person has one of these documents, otherwise null.

### Example
#### Validator

Validations return true or false following the laravel validation convention.

```php
$request->validate([
    'identification' => 'ecuador:personal_identification',
]);
```

#### Facade

Facades return null if the document number does not match any type, otherwise they return the billing code.

```php
use Luilliarcec\LaravelEcuadorIdentification\Facades\EcuadorIdentification;

EcuadorIdentification::validateAllIdentificatons('9999999999999'); // Return '07' => Final Consumer
```

## Translations

If you like to use the translation system of Laravel to present the messages or attributes. Access the corresponding 
files located in the ``resources\lang\{language_code}\validation`` folder.

##### Example
``resources\lang\en\validation:``
```php
    
    return [
        ...

        'ecuador' => 'The :attribute field does not have the corresponding country format. (Ecuador)',
    
        /*
        |--------------------------------------------------------------------------
        | Custom Validation Attributes
        |--------------------------------------------------------------------------
        |
        | The following language lines are used to swap our attribute placeholder
        | with something more reader friendly such as "E-Mail Address" instead
        | of "email". This simply helps us make our message more expressive.
        |
        */
    
        'attributes' => [
            'id' => 'Ecuadorian Identification',
        ],
    ];
```

``resources\lang\es\validation:``
```php
    
    return [
        ...

        'ecuador' => 'El campo :attribute no tiene el formato de país correspondiente. (Ecuador)',
    
        /*
        |--------------------------------------------------------------------------
        | Custom Validation Attributes
        |--------------------------------------------------------------------------
        |
        | The following language lines are used to swap our attribute placeholder
        | with something more reader friendly such as "E-Mail Address" instead
        | of "email". This simply helps us make our message more expressive.
        |
        */
    
        'attributes' => [
            'id' => 'Identificación Ecuatoriana',
        ],
    ];
```


### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email luilliarcec@gmail.com instead of using the issue tracker.

## Credits

- [Luis Andrés Arce C.](https://github.com/luilliarcec)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
