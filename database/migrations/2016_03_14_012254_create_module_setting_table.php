<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_setting', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->string('property');
            $table->integer('module_id')->unsigned();
        });

        Schema::table('module_setting', function($table) {
            $table->foreign('module_id')
                ->references('id')->on('module')
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
        Schema::drop('module_setting');
    }
}
