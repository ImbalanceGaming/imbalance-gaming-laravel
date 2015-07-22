<?php

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use imbalance\Models\User;

class UserTableSeeder extends Seeder {

    public function run() {

        $faker = Faker::create();

        foreach(range(1, 30) as $index) {
            $user = User::create([
                'username'=>$faker->userName,
                'password'=>Hash::make($faker->password()),
                'email'=>$faker->email
            ]);

            $userDetails = new \imbalance\Models\UserDetails([
                'forename'=>$faker->firstName,
                'surname'=>$faker->lastName,
                'dob'=>$faker->date(),
                'country'=>$faker->country,
                'website'=>$faker->url,
                'avatar'=>$faker->image('resources/avatars', 60, 60),
                'twitterUsername'=>$user->username,
                'facebook'=>$faker->url
            ]);

            $user->userDetails()->save($userDetails);
        }

    }

}