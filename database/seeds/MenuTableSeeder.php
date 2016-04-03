<?php

use Illuminate\Database\Seeder;
use imbalance\Models\Menu;

class MenuTableSeeder extends Seeder {

    public function run() {

        $menu = Menu::create([
            "name"=>'Management Module',
            "description"=>"Management module for Imbalance Gaming site.",
            "placement"=>"main",
            "module_section_id"=>1,
            "component_id"=>1
        ]);

        print "Menu: ".$menu->name."\n";

    }

}