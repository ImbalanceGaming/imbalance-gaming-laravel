<?php

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use imbalance\Models\User;

class UserTableSeeder extends Seeder {

    public function run() {

        $faker = Faker::create();

        $password = '10Banana12';

        $user = User::create([
            'username'=>'c.pratt',
            'password'=>Hash::make($password),
            'email'=>'chrispratt1985@gmail.com',
            'role'=>'Administrator',
            'email_verified'=>true,
            'forename'=>'Christopher',
            'surname'=>'Pratt',
            'dob'=>$faker->date(),
            'country'=>$faker->country,
            'website'=>$faker->url,
            'avatar'=>$faker->image('resources/avatars', 60, 60),
//            'avatar'=>'',
            'twitter_username'=>'c.pratt',
            'facebook'=>$faker->url,
            'has_dev_area'=>true
        ]);

        print "Email: ".$user->email." Password: ".$password."\n";

        $password = 'imbalanceAdmin';
        $user = User::create([
            'username'=>'admin',
            'password'=>Hash::make($password),
            'email'=>'admin@imbalancegaming.com',
            'role'=>'Administrator',
            'email_verified'=>true,
            'forename'=>'Admin',
            'surname'=>'User'
        ]);

        print "Email: ".$user->email." Password: ".$password."\n";

        factory(\imbalance\Models\User::class, 30)->create()->each(function(\imbalance\Models\User $u) {
            print "Email: ".$u->email."\n";
        });

    }

}