<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\MockEntities\SessionType;
use Faker\Generator as Faker;

$factory->define(\App\MockEntities\SessionActivity::class, function (Faker $faker) {
    return [
        'name'      => $faker->lexify("????????"),
        'code'      => $faker->lexify('??')
    ];
});
