<?php

use Illuminate\Database\Seeder;
use imbalance\Models\Component;

class ComponentTableSeeder extends Seeder {

    public function run() {

        $component = Component::create([
            "name"=>'User Management',
            "description"=>"Component details for user management module section",
            "path"=>"/usermanagement/...",
            "component_path"=>"./userManagement/user.management.component",
            "component_name"=>"UserManagementComponent"
        ]);

        print "Component: ".$component->name."\n";

    }

}