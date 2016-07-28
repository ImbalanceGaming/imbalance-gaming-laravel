<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOutputToProjectDeploymentHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_deployment_history', function (Blueprint $table) {
            $table->text('job_output')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_deployment_history', function (Blueprint $table) {
            $table->dropColumn('job_output');
        });
    }
}
