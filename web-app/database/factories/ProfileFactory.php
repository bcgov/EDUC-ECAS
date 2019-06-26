<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\MockEntities\Profile;
use Faker\Generator as Faker;

$factory->define(Profile::class, function (Faker $faker) {

    $postal_code = 'V'. $faker->randomDigitNotNull . $faker->randomLetter .
        $faker->randomDigitNotNull . $faker->randomLetter . $faker->randomDigitNotNull;

    $districts = \App\MockEntities\District::pluck('id')->toArray();
    $schools =  \App\MockEntities\School::pluck('id')->toArray();

    return [
        //'id'                             => 'contactid', // TODO: also need to change it here
        'preferred_first_name'           =>  $faker->firstName,
        'first_name'                     =>  $faker->firstName,
        'last_name'                      =>  $faker->lastName,
        'email'                          =>  $faker->email,
        'phone'                          =>  $faker->phoneNumber,
        'social_insurance_number'        =>  $faker->numberBetween(100000000,999999999),
        'address_1'                      =>  $faker->streetAddress,
        'address_2'                      =>  $faker->streetAddress,
        'city'                           =>  $faker->city,
        'region'                         =>  $faker->randomElement(['BC','YK']),
        'postal_code'                    =>  strtoupper($postal_code),
        'district_id'                    =>  $faker->randomElement($districts),
        'school_id'                      =>  $faker->randomElement($schools),
        'professional_certificate_bc'    => 'bc_' . $faker->lexify('????????'),
        'professional_certificate_yk'    => 'yk_' . $faker->lexify('????????'),
        'professional_certificate_other' => 'other_' . $faker->lexify('????????'),
    ];
});
