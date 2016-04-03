<?php

use Illuminate\Database\Seeder;

class ProjectTableSeeder extends Seeder {

    public function run() {

        factory(\imbalance\Models\Project::class)->create(["user_id"=>1])->each(function(\imbalance\Models\Project $p) {
            $p->groups()->attach(3);
            print "Project: ".$p->name."\n";
        });

    }

}