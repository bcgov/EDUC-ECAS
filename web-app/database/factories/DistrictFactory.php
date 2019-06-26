<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\MockEntities\District;
use Faker\Generator as Faker;

$factory->define(District::class, function (Faker $faker) {
    return [

        'name'   =>   $faker->city . ' District'
    ];
});
