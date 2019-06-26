<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\MockEntities\Role;
use Faker\Generator as Faker;

$factory->define(Role::class, function (Faker $faker) {
    return [

        'name' => $faker->randomElement(['Admin', 'Other']),
        'rate' => $faker->numberBetween(90,125)
    ];
});
