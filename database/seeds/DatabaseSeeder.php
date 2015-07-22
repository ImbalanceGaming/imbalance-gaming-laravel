<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use imbalance\Models\User;
use imbalance\Models\UserDetails;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        UserDetails::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Model::unguard();

         $this->call(UserTableSeeder::class);

        Model::reguard();
    }
}
