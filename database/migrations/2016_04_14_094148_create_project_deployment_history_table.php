<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectDeploymentHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_deployment_history', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('deployment_date');
            $table->string('user');
            $table->string('server');
            $table->string('status');
            $table->integer('project_id')->unsigned();
        });

        Schema::table('project_deployment_history', function($table) {
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
        Schema::drop('project_deployment_history');
    }
}
