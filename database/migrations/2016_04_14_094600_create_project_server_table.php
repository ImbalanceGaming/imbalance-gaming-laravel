<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectServerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_server', function (Blueprint $table) {
            $table->integer('project_id')->unsigned();
            $table->integer('server_id')->unsigned();
            $table->boolean('first_run')->default(true);

            $table->primary(['project_id', 'server_id']);
        });

        Schema::table('project_server', function($table) {
            $table->foreign('project_id')
                ->references('id')->on('project')
                ->onDelete('cascade');

            $table->foreign('server_id')
                ->references('id')->on('server')
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
        Schema::drop('project_server');
    }
}
