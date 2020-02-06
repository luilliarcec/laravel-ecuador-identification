<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    /*
    |--------------------------------------------------------------------------
    | Provinces
    |--------------------------------------------------------------------------
    |
    | Present the number of provinces in the country
    |
    */

    'provinces' => 24,

    /*
    |--------------------------------------------------------------------------
    | Types of identification
    |--------------------------------------------------------------------------
    |
    | General configuration of identification types
    |
    */

    'type-identifications' => [
        'ruc' => [
            'length' => 13,
            'billing-code' => '04'
        ],

        'personal-identification' => [
            'length' => 10,
            'billing-code' => '05',
        ],

        'final-customer' => [
            'length' => 13,
            'billing-code' => '07'
        ]
    ],


    /*
    |--------------------------------------------------------------------------
    | Private RUC
    |--------------------------------------------------------------------------
    |
    | Represents the RUC configuration of private companies
    |
    */

    'private-ruc' => [
        'third-digit' => 9,
        'last-digits' => '001',
        'coefficients' => [4, 3, 2, 7, 6, 5, 4, 3, 2],
        'check-digit-position' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Public RUC
    |--------------------------------------------------------------------------
    |
    | Represents the RUC configuration of public companies
    |
    */

    'public-ruc' => [
        'third-digit' => 6,
        'last-digits' => '0001',
        'coefficients' => [3, 2, 7, 6, 5, 4, 3, 2],
        'check-digit-position' => 9,
    ],

    /*
    |--------------------------------------------------------------------------
    | Natural RUC depends on Personal Identification
    |--------------------------------------------------------------------------
    |
    | Represents the RUC configuration of natural people
    |
    */

    'natural-ruc' => [
        'last-digits' => '001',
    ],

    /*
    |--------------------------------------------------------------------------
    | Personal Identification (Cédula)
    |--------------------------------------------------------------------------
    |
    | Represents the Personal Identification (Cédula) configuration of the all people
    |
    */

    'personal-identification' => [
        'third-digit' => [
            'min' => 0,
            'max' => 5
        ],
        'coefficients' => [2, 1, 2, 1, 2, 1, 2, 1, 2],
        'check-digit-position' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Final customer
    |--------------------------------------------------------------------------
    |
    | Represents the Personal Identification (Cédula) configuration of the all people
    |
    */

    'final-customer' => [
        'unique-value' => '9999999999999',
    ],
];
