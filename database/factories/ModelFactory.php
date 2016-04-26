<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(imbalance\Models\User::class, function (Faker\Generator $faker) {

    $username = $faker->userName;
    $password = $faker->password();
    return [
        'username'=>$username,
        'password'=>Hash::make($password),
        'email'=>$faker->email,
        'role'=>'User',
        'email_verified'=>true,
        'forename'=>$faker->firstName,
        'surname'=>$faker->lastName,
        'dob'=>$faker->date(),
        'country'=>$faker->country,
        'website'=>$faker->url,
        'avatar'=>null,
        'twitter_username'=>$username,
        'facebook'=>$faker->url
    ];

});

$factory->define(imbalance\Models\Project::class, function (Faker\Generator $faker) {

    $name = $faker->word;
    
    return [
        'key'=>ucfirst(substr($name, 0, 1)),
        'name'=>ucfirst($name),
        'description'=>$faker->sentence,
        'user_id'=>null
    ];

});
