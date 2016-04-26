<?php

use Illuminate\Database\Seeder;
use imbalance\Models\Server;

class ProjectServerTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        
        $server = Server::create([
            'name' => 'Dev Server',
            'address' => '192.168.0.2'
        ]);

        $server->projects()->attach(1);
        
        print "Created server ".$server->name."\n";

        $server = Server::create([
            'name' => 'Azure Live Server',
            'address' => '104.47.137.147'
        ]);

        $server->projects()->attach(1);

        print "Created server ".$server->name."\n";

    }
    
}