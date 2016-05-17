<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectPackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_package', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('repository');
            $table->string('deploy_branch');
            $table->string('deploy_location');
            $table->integer('order');
            $table->integer('project_id')->unsigned();
        });

        Schema::table('project_package', function($table) {
            $table->foreign('project_id')
                ->references('id')->on('project')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('project_package');
    }
}
