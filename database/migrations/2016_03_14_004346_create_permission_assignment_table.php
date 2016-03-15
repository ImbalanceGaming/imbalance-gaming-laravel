<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionAssignmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission_assignment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->unsigned();
            $table->integer('group_id')->nullable()->unsigned();
            $table->integer('permission_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('permission_assignment', function($table) {
            $table->foreign('user_id')
                ->references('id')->on('user')
                ->onDelete('cascade');
        });

        Schema::table('permission_assignment', function($table) {
            $table->foreign('group_id')
                ->references('id')->on('group')
                ->onDelete('cascade');
        });

        Schema::table('permission_assignment', function($table) {
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
        Schema::drop('permission_assignment');
    }
}
