<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Dynamics\Mock\Credential;
use Faker\Generator as Faker;

$factory->define(Credential::class, function (Faker $faker) {
    return [
        'name'      => $faker->word . ' Credential'
    ];
});
