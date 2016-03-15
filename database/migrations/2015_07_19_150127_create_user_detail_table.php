<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->string('forename', 100)->nullable();
            $table->string('surname', 100)->nullable();
            $table->date('dob')->nullable();
            $table->string('country', 100)->nullable();
            $table->string('website', 200)->nullable();
            $table->string('avatar', 200)->nullable();
            $table->string('twitterUsername', 200)->nullable();
            $table->string('facebook', 200)->nullable();
            $table->integer('user_id')->unsigned();
        });

        Schema::table('user_detail', function($table) {
            $table->foreign('user_id')
                ->references('id')->on('user')
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
        Schema::drop('user_detail');
    }
}
