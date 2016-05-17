<?php

use Illuminate\Database\Seeder;
use imbalance\Models\ModuleSection;

class ModuleSectionTableSeeder extends Seeder {

    public function run() {

        /** @var ModuleSection $moduleSection */
        $moduleSection = ModuleSection::create([
            "name"=>'User Management',
            "description"=>"Functionality for managing users, groups and permissions",
            "module_id"=>1
        ]);

        print "Module Section: ".$moduleSection->name."\n";

        /** @var ModuleSection $moduleSection */
        $moduleSection = ModuleSection::create([
            "name"=>'Users',
            "description"=>"Functionality for managing users",
            "module_id"=>1
        ]);

        print "Module Section: ".$moduleSection->name."\n";

        /** @var ModuleSection $moduleSection */
        $moduleSection = ModuleSection::create([
            "name"=>'Groups',
            "description"=>"Functionality for managing groups",
            "module_id"=>1
        ]);

        print "Module Section: ".$moduleSection->name."\n";

        /** @var ModuleSection $moduleSection */
        $moduleSection = ModuleSection::create([
            "name"=>'Permissions',
            "description"=>"Functionality for managing permissions",
            "module_id"=>1
        ]);

        print "Module Section: ".$moduleSection->name."\n";

        $moduleSection = ModuleSection::create([
            "name"=>'Projects',
            "description"=>'Functionality for managing projects',
            "module_id"=>1
        ]);

        print "Module Section: ".$moduleSection->name."\n";

        $moduleSection = ModuleSection::create([
            "name"=>'Modules',
            "description"=>'Functionality for managing modules',
            "module_id"=>1
        ]);

        print "Module Section: ".$moduleSection->name."\n";

    }

}