<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('forename', 100)->nullable();
            $table->string('surname', 100)->nullable();
            $table->date('dob')->nullable();
            $table->string('country', 100)->nullable();
            $table->string('website', 200)->nullable();
            $table->string('avatar', 200)->nullable();
            $table->string('twitterUsername', 200)->nullable();
            $table->string('facebook', 200)->nullable();
        });

        Schema::table('user_details', function($table) {
            $table->foreign('user_id')
                ->references('id')->on('users')
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
        Schema::drop('user_details');
    }
}
