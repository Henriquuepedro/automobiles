<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutomoveisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('automoveis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tipo_auto', 20);
            $table->integer('marca_id');
            $table->string('marca_nome', 512);
            $table->integer('modelo_id');
            $table->string('modelo_nome', 512);
            $table->string('ano_id', 6);
            $table->integer('ano_nome');
            $table->float('valor', 8, 2);
            $table->string('cor', 512);
            $table->tinyInteger('unico_dono');
            $table->tinyInteger('aceita_troca');
            $table->string('placa', 8);
            $table->tinyInteger('final_placa');
            $table->integer('kms')->default(0);
            //$table->string('combustivel');
            $table->boolean('destaque');
            $table->bigInteger('user_insert')->unsigned();
            $table->bigInteger('user_update')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('user_insert')->references('id')->on('users');
            $table->foreign('user_update')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('automoveis');
    }
}
