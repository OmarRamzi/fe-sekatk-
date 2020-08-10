<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use App\Ride;
use Illuminate\Support\Str;



$factory->define(Ride::class, function (Faker $faker) {
    return [
        'user_id'=>1,
                'startPointLatitude'=>324324.1234324,
                'startPointLongitude'=>324324.1234324,
                'destinationLatitude'=>324324.1234324,
                'destinationLongitude'=>324324.1234324,
                'destinationLongitude'=>324324.1234324,
                'availableSeats'=>rand(1,3),
                'time' => now(),
    ];
});
