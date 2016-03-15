<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleSubSectionItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_sub_section_item', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('link');
            $table->integer('menu_sub_section_id')->unsigned();
        });

        Schema::table('menu_sub_section_item', function($table) {
            $table->foreign('menu_sub_section_id')
                ->references('id')->on('menu_sub_section')
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
        Schema::drop('menu_sub_section_item');
    }
}
