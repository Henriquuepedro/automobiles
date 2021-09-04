<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexFipeYearsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fipe_years', function (Blueprint $table) {
            $table->index(['type_auto', 'brand_id', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fipe_years', function (Blueprint $table) {
            $table->index(['type_auto', 'brand_id', 'model_id']);
        });
    }
}
