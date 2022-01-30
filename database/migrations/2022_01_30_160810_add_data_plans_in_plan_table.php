<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataPlansInPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('id_plan')->after('plan');
            $table->string('type_plan')->after('plan');
            $table->renameColumn('plan', 'name_plan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan', function (Blueprint $table) {
            $table->dropColumn('id_plan');
            $table->dropColumn('type_plan');
            $table->renameColumn('name_plan', 'plan');
        });
    }
}
