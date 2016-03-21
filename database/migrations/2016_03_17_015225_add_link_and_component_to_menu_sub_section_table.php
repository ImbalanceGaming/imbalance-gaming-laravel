<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLinkAndComponentToMenuSubSectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_sub_section', function (Blueprint $table) {
            $table->string('link')->nullable();
            $table->string('component')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_sub_section', function (Blueprint $table) {
            $table->dropColumn(['link', 'component']);
        });
    }
}
