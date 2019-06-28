<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\MockEntities\Session;
use Faker\Generator as Faker;

$factory->define(Session::class, function (Faker $faker) {

    $start_date = $faker->dateTimeBetween('now', '+1year');

    $types = \App\MockEntities\SessionType::pluck('id')->toArray();
    $activities = \App\MockEntities\SessionActivity::pluck('id')->toArray();

    return [

        'activity_id'   => $faker->randomElement($activities),
        'type_id'       => $faker->randomElement($types),

        'start_date'    => $start_date,
        'end_date'      => date_modify($start_date, '+' . $faker->randomDigit . 'days'),

        'location'      => $faker->lastName . $faker->randomElement(['', ' Secondary', ' Middle']) . ' School',
        'address'       => $faker->streetAddress,
        'city'          => $faker->city,

    ];
});
