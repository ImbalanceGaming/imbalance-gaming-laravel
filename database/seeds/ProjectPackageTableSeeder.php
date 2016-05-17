<?php

use Illuminate\Database\Seeder;
use imbalance\Models\ProjectPackage;

class ProjectPackageTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $package = ProjectPackage::create([
            'name' => 'Angular 2 Front End',
            'repository' => 'https://github.com/ImbalanceGaming/imbalance-gaming-management-interface-angular-2.git',
            'deploy_branch' => 'master',
            'deploy_location' => 'imbalance',
            'project_id' => 1,
            'order' => 1
        ]);

        print "Project package: ".$package->name."\n";

        $package = ProjectPackage::create([
            'name' => 'Laravel API',
            'repository' => 'https://github.com/ImbalanceGaming/imbalance-gaming-laravel.git',
            'deploy_branch' => 'master',
            'deploy_location' => 'imbalance/api',
            'project_id' => 1,
            'order' => 2
        ]);

        print "Project package: ".$package->name."\n";

        $package = ProjectPackage::create([
            'name' => 'CatchX',
            'repository' => 'https://github.com/ImbalanceGaming/CatchX.git',
            'deploy_branch' => 'master',
            'deploy_location' => 'catchx',
            'project_id' => 2,
            'order' => 1
        ]);

        print "Project package: ".$package->name."\n";

    }

}