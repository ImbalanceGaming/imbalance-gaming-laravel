<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('url')->nullable();
            $table->integer('user_id')->unsigned()->nullable();
        });

        Schema::table('project', function($table) {
            $table->foreign('user_id')
                ->references('id')->on('user')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('project');
    }
}
