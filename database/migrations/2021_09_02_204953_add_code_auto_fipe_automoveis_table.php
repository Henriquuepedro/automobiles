<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodeAutoFipeAutomoveisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('automoveis', function (Blueprint $table) {
            $table->bigInteger('code_auto_fipe')->nullable()->unsigned();
            $table->foreign('code_auto_fipe')->references('id')->on('fipe_autos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('automoveis', function (Blueprint $table) {
            $table->dropColumn('code_auto_fipe');
        });
    }
}
