<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNameComplementarToComplementaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('complementar_auto', 'complementary_auto');
        Schema::rename('complementar_autos', 'complementaries');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('complementary_auto', 'complementar_auto');
        Schema::rename('complementaries', 'complementar_autos');
    }
}
