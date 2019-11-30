<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstadofinanceiroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estadofinanceiro', function (Blueprint $table) {
            $table->bigIncrements('NCODESTADOFINANCEIRO');
            $table->integer('NCODAUTO')->references('NCODAUTO')->on('automoveis')->onDelete('cascade');
            $table->tinyInteger('FINANCIADO');
            $table->tinyInteger('MULTAS');
            $table->tinyInteger('IPVAPAGO');
            $table->tinyInteger('LEILAO');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estadofinanceiro');
    }
}
