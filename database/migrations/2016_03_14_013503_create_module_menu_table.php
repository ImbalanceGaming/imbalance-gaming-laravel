<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('placement');
            $table->integer('module_section_id')->unsigned();
            $table->integer('component_id')->unsigned()->nullable();
        });

        Schema::table('menu', function($table) {
            $table->foreign('module_section_id')
                ->references('id')->on('module_section')
                ->onDelete('cascade');

            $table->foreign('component_id')
                ->references('id')->on('component')
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
        Schema::drop('menu');
    }
}
