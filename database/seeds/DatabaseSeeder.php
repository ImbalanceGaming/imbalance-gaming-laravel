<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{

    private $_tableNames = [
        'user',
        'project',
        'group',
        'group_membership',
        'project_group',
        'module',
        'module_section',
        'component',
        'menu',
        'permission_assignment',
        'module_section_access',
        'permission',
        'project_package',
        'project_package_command',
        'project_package_command_type',
        'server'
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
        $this->call(GroupTableSeeder::class);
        $this->call(ProjectTableSeeder::class);
        $this->call(ModuleTableSeeder::class);
        $this->call(ModuleSectionTableSeeder::class);
        $this->call(ComponentTableSeeder::class);
        $this->call(MenuTableSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(ProjectPackageTableSeeder::class);
        $this->call(ProjectPackageCommandTypeTableSeeder::class);
        $this->call(ProjectPackageCommandTableSeeder::class);
        $this->call(ProjectServerTableSeeder::class);

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
