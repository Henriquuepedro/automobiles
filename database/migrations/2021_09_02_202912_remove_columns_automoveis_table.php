<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveColumnsAutomoveisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('automoveis', function (Blueprint $table) {
            $table->dropColumn('marca_id');
            $table->dropColumn('marca_nome');
            $table->dropColumn('modelo_id');
            $table->dropColumn('modelo_nome');
            $table->dropColumn('ano_id');
            $table->dropColumn('ano_nome');
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
            $table->integer('marca_id');
            $table->string('marca_nome', 512);
            $table->integer('modelo_id');
            $table->string('modelo_nome', 512);
            $table->string('ano_id', 6);
            $table->integer('ano_nome');
        });
    }
}
