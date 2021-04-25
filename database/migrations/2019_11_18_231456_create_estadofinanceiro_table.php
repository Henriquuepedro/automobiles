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
            $table->bigIncrements('id');
            $table->bigInteger('auto_id')->unsigned();
            $table->string('valores')->comment('JSON com os ids dos estados financeiros da conta');
            $table->timestamps();

            $table->foreign('auto_id')->references('id')->on('automoveis')->onDelete('cascade');
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
