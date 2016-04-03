<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_group', function (Blueprint $table) {
            $table->integer('project_id')->unsigned();
            $table->integer('group_id')->unsigned();

            $table->primary(['project_id', 'group_id']);
        });

        Schema::table('project_group', function($table) {
            $table->foreign('project_id')
                ->references('id')->on('project')
                ->onDelete('cascade');

            $table->foreign('group_id')
                ->references('id')->on('group')
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
        Schema::drop('project_group');
    }
}
