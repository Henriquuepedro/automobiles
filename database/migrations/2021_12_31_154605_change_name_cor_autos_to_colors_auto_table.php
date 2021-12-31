<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNameCorAutosToColorsAutoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('cor_autos', 'colors_auto');
        Schema::table('automobiles', function (Blueprint $table) {
            $table->bigInteger('cor')->unsigned()->nullable()->change();
            $table->foreign('cor')->references('id')->on('colors_auto')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('automobiles', function (Blueprint $table) {
            $table->dropForeign('automobiles_cor_foreign');
            $table->string('cor', 512)->change();
        });
        Schema::rename('colors_auto', 'cor_autos');
    }
}
