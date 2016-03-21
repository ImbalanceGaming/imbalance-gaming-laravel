<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{

    private $_tableNames = [
        'user',
        'user_detail'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $this->cleanDatabase();

        Model::unguard();

        $this->call(UserTableSeeder::class);

        Model::reguard();

    }

    public function cleanDatabase() {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($this->_tableNames as $tableName) {
            DB::table($tableName)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }
}
