<?php

use Illuminate\Database\Seeder;
use imbalance\Models\ProjectPackageCommandType;

class ProjectPackageCommandTypeTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $commandType = ProjectPackageCommandType::create([
            'name' => 'Composer'
        ]);

        print "Created project package command type ".$commandType->name."\n";

        $commandType = ProjectPackageCommandType::create([
            'name' => 'Npm'
        ]);

        print "Created project package command type ".$commandType->name."\n";

        $commandType = ProjectPackageCommandType::create([
            'name' => 'System'
        ]);

        print "Created project package command type ".$commandType->name."\n";

    }

}