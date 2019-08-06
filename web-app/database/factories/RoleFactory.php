<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(\App\MockEntities\Role::class, function (Faker $faker) {
    return [

        'name'                           =>  $faker->city,
        'rate'                           =>  $faker->numberBetween(290, 400)

    ];
});
