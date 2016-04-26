<?php

use Illuminate\Database\Seeder;
use imbalance\Models\Project;

class ProjectTableSeeder extends Seeder {

    public function run() {

        $project = Project::create([
            'key'=>'TP',
            'name'=>'Imbalance Gaming Management Interface',
            'description'=>'An awsome project for my final year project',
            'url' => 'http://www.imbalancegaming.com/',
            'user_id'=>1
        ]);

        print "Project: ".$project->name."\n";

        factory(Project::class)->create(["user_id"=>1])->each(function(Project $p) {
            $p->groups()->attach(3);
            print "Project: ".$p->name."\n";
        });

    }

}