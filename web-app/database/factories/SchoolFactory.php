<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\MockEntities\School;
use Faker\Generator as Faker;

$factory->define(School::class, function (Faker $faker) {
    return [

        'name'                           =>  $faker->city . ' School',
        'city'                           =>  $faker->city,

    ];
});
