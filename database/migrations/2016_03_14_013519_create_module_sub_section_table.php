<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleSubSectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_sub_section', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('menu_id')->unsigned();
        });

        Schema::table('menu_sub_section', function($table) {
            $table->foreign('menu_id')
                ->references('id')->on('menu')
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
        Schema::drop('menu_sub_section');
    }
}
