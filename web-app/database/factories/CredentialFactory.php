<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */


use Faker\Generator as Faker;

$factory->define(\App\MockEntities\Credential::class, function (Faker $faker) {
    return [
        'name'      => ucfirst($faker->lexify('????????') . ' Credential')
    ];
});
