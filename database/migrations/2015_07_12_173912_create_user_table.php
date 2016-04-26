<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('password', 60);
            $table->string('email')->unique();
            $table->string('role')->default('User');
            $table->boolean('email_verified')->default(false);
            $table->string('email_verified_code');
            $table->boolean('active')->default(true);
            $table->dateTime('last_login');
            $table->string('forename', 100)->nullable();
            $table->string('surname', 100)->nullable();
            $table->date('dob')->nullable();
            $table->string('country', 100)->nullable();
            $table->string('website', 200)->nullable();
            $table->string('avatar', 200)->nullable();
            $table->string('twitter_username', 200)->nullable();
            $table->string('facebook', 200)->nullable();
            $table->boolean('has_dev_area')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user');
    }
}
