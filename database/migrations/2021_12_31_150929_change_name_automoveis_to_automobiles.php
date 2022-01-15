<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNameTablesToEnglishTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // rename automoveis to automobiles
        Schema::table('opcional', function (Blueprint $table) {
            $table->dropForeign('opcional_auto_id_foreign');
        });
        Schema::table('imagensauto', function (Blueprint $table) {
            $table->dropForeign('imagensauto_auto_id_foreign');
        });
        Schema::table('estadofinanceiro', function (Blueprint $table) {
            $table->dropForeign('estadofinanceiro_auto_id_foreign');
        });
        Schema::table('complementar_auto', function (Blueprint $table) {
            $table->dropForeign('complementar_auto_auto_id_foreign');
        });

        Schema::rename('automoveis', 'automobiles');

        Schema::table('opcional', function (Blueprint $table) {
            $table->foreign('auto_id')->references('id')->on('automobiles')->onDelete('cascade');
        });
        Schema::table('imagensauto', function (Blueprint $table) {
            $table->foreign('auto_id')->references('id')->on('automobiles')->onDelete('cascade');
        });
        Schema::table('estadofinanceiro', function (Blueprint $table) {
            $table->foreign('auto_id')->references('id')->on('automobiles')->onDelete('cascade');
        });
        Schema::table('complementar_auto', function (Blueprint $table) {
            $table->foreign('auto_id')->references('id')->on('automobiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // rename automobiles to automoveis
        Schema::table('opcional', function (Blueprint $table) {
            $table->dropForeign('opcional_auto_id_foreign');
        });
        Schema::table('imagensauto', function (Blueprint $table) {
            $table->dropForeign('imagensauto_auto_id_foreign');
        });
        Schema::table('estadofinanceiro', function (Blueprint $table) {
            $table->dropForeign('estadofinanceiro_auto_id_foreign');
        });
        Schema::table('complementar_auto', function (Blueprint $table) {
            $table->dropForeign('complementar_auto_auto_id_foreign');
        });

        Schema::rename('automobiles', 'automoveis');

        Schema::table('opcional', function (Blueprint $table) {
            $table->foreign('auto_id')->references('id')->on('automoveis')->onDelete('cascade');
        });
        Schema::table('imagensauto', function (Blueprint $table) {
            $table->foreign('auto_id')->references('id')->on('automoveis')->onDelete('cascade');
        });
        Schema::table('estadofinanceiro', function (Blueprint $table) {
            $table->foreign('auto_id')->references('id')->on('automoveis')->onDelete('cascade');
        });
        Schema::table('complementar_auto', function (Blueprint $table) {
            $table->foreign('auto_id')->references('id')->on('automoveis')->onDelete('cascade');
        });
    }
}
