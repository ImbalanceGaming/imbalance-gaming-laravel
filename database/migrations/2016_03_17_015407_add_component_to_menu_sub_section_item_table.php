<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddComponentToMenuSubSectionItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_sub_section_item', function (Blueprint $table) {
            $table->string('component');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_sub_section_item', function (Blueprint $table) {
            $table->dropColumn('component');
        });
    }
}
