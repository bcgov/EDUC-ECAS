<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\MockEntities\ProfileCredential;
use Faker\Generator as Faker;

$factory->define(ProfileCredential::class, function (Faker $faker) {

    $credentials = \App\MockEntities\Credential::pluck('id')->toArray();
    $users = \App\MockEntities\Profile::pluck('id')->toArray();

    return [
        'user_id'       => $faker->randomElement($users),
        'credential_id' => $faker->randomElement($credentials),
        'verified'      => $faker->randomElement(['Yes','No','Unverified']),
    ];
});
