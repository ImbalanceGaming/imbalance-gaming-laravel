<?php

use Illuminate\Database\Seeder;
use imbalance\Models\Permission;

class PermissionTableSeeder extends Seeder {

    public function run() {

        $permission = Permission::create([
            'name'=>'Management Interface Access',
            'description'=>'Base permission to be able to access the management interface',
            'view'=>true,
            'add'=>false,
            'edit'=>false,
            'delete'=>false
        ]);

        $permission->groups()->attach(2);
        $permission->groups()->attach(3);

        print "Permission: ".$permission->name."\n";

        $permission = Permission::create([
            'name'=>'User Management View',
            'description'=>'View access to user management section',
            'view'=>false,
            'add'=>false,
            'edit'=>false,
            'delete'=>false
        ]);

        $permission->groups()->attach(2);
        $permission->moduleSections()->attach(1);

        print "Permission: ".$permission->name."\n";

        $permission = Permission::create([
            'name'=>'User Management Add, Edit & View',
            'description'=>'Base developer permission with view access to management interface',
            'view'=>true,
            'add'=>true,
            'edit'=>true,
            'delete'=>false
        ]);

        $permission->moduleSections()->attach(1);

        print "Permission: ".$permission->name."\n";

        $permission = Permission::create([
            'name'=>'User Management Full Access',
            'description'=>'Base admin permission with full access to management interface',
            'view'=>true,
            'add'=>true,
            'edit'=>true,
            'delete'=>true
        ]);

        $permission->moduleSections()->attach(1);

        print "Permission: ".$permission->name."\n";

        $permission = Permission::create([
            'name'=>'Admin Access',
            'description'=>'Admin access to site giving full access to all areas',
            'view'=>true,
            'add'=>true,
            'edit'=>true,
            'delete'=>true
        ]);

        $permission->groups()->attach(3);

        print "Permission: ".$permission->name."\n";



    }

}