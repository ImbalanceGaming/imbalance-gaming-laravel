<?php

use Illuminate\Database\Seeder;
use imbalance\Models\Project;

class ProjectTableSeeder extends Seeder {

    public function run() {

        $project = Project::create([
            'key'=>'IGMI',
            'name'=>'Imbalance Gaming Management Interface',
            'description'=>'An awesome project for my final year project',
            'url' => 'http://www.imbalancegaming.com/',
            'user_id'=>1
        ]);

        print "Project: ".$project->name."\n";

        $project = Project::create([
            'key'=>'C',
            'name'=>'CatchX',
            'description'=>'A turn based game based around batman',
            'url' => 'http://catchx.imbalancegaming.com',
            'user_id'=>1
        ]);

        print "Project: ".$project->name."\n";

//        factory(Project::class)->create(["user_id"=>1])->each(function(Project $p) {
//            $p->groups()->attach(3);
//            print "Project: ".$p->name."\n";
//        });

    }

}