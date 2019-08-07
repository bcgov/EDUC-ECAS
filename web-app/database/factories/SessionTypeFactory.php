<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\MockEntities\SessionType;
use Faker\Generator as Faker;

$factory->define(SessionType::class, function (Faker $faker) {



    return $faker->randomElement(\App\MockEntities\SeedData::$session_types);
});
