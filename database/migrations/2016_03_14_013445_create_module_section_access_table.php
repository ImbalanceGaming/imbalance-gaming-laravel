<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleSectionAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_section_access', function (Blueprint $table) {
            $table->integer('section_id')->unique()->unsigned();
            $table->integer('permission_id')->unique()->unsigned();
        });

        Schema::table('module_section_access', function($table) {
            $table->foreign('section_id')
                ->references('id')->on('module_section')
                ->onDelete('cascade');

            $table->foreign('permission_id')
                ->references('id')->on('permission')
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
        Schema::drop('module_section_access');
    }
}
