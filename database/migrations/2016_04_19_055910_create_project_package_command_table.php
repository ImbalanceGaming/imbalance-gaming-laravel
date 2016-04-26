<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectPackageCommandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_package_command', function (Blueprint $table) {
            $table->increments('id');
            $table->string('command');
            $table->integer('order');
            $table->string('run_on');
            $table->integer('project_package_command_type_id')->unsigned();
            $table->integer('project_package_id')->unsigned();
        });

        Schema::table('project_package_command', function($table) {
            $table->foreign('project_package_command_type_id')
                ->references('id')->on('project_package_command_type')
                ->onDelete('cascade');

            $table->foreign('project_package_id')
                ->references('id')->on('project_package')
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
        Schema::drop('project_package_command');
    }
}
