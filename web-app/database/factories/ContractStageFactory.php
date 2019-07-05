<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\MockEntities\ContractStage::class, function (Faker $faker) {
    return [
        'name'      => $faker->lexify('???????'),
    ];
});
