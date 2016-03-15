<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->boolean('delete');
            $table->boolean('edit');
            $table->boolean('view');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('permission');
    }
}
