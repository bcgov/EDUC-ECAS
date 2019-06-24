<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Dynamics\Mock\ProfileCredential;
use Faker\Generator as Faker;

$factory->define(ProfileCredential::class, function (Faker $faker) {

    $credentials = \App\Dynamics\Mock\Credential::pluck('id')->toArray();
    $users = \App\User::pluck('id')->toArray();

    return [
        'user_id'       => $faker->randomElement($users),
        'credential_id' => $faker->randomElement($credentials),
        'verified'      => $faker->boolean,
    ];
});
