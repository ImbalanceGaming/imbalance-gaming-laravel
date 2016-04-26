<?php

use Illuminate\Database\Seeder;
use imbalance\Models\ProjectPackageCommand;

class ProjectPackageCommandTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $command = ProjectPackageCommand::create([
            'command' => 'composer install',
            'order' => 1,
            'run_on' => 'install',
            'project_package_command_type_id' => 1,
            'project_package_id' => 2
        ]);

        print "Created package command ".$command->command."\n";

        $command = ProjectPackageCommand::create([
            'command' => 'npm install',
            'order' => 1,
            'run_on' => 'install',
            'project_package_command_type_id' => 2,
            'project_package_id' => 1
        ]);

        print "Created package command ".$command->command."\n";

    }

}