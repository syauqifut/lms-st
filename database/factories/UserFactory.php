<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'fullname' => $faker->firstName,
        'email' => $faker->unique()->safeEmail,
        'password' => 'superadmin',
        'remember_token' => Str::random(10),
        'owner' => false,
        'email' => $faker->email,
        'owner' => true,
        'username' => $faker->firstName,
        'adress' => 'required',
        'city' => 'required',
        'country' => 'required',
        'mobilephone' => 'required',
        'birthplace' => 'required',
        'birthdate' => Carbon::now(),
        'usertype_id' => 1,
        'is_active' => 1,
        'createdBy' => 1,
    ];
});
