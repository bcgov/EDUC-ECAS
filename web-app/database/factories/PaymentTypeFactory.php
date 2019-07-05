<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\MockEntities\Payment;
use Faker\Generator as Faker;

$factory->define(Payment::class, function (Faker $faker) {
    return [
        'name'      => $faker->lexify("????????"),
    ];
});
