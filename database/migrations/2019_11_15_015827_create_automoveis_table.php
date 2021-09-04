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
            $table->float('valor', 12, 2);
            $table->string('cor', 512);
            $table->tinyInteger('unico_dono');
            $table->tinyInteger('aceita_troca');
            $table->string('placa', 8);
            $table->tinyInteger('final_placa');
            $table->integer('kms')->default(0);
            //$table->string('combustivel');
            $table->boolean('destaque');
            $table->bigInteger('company_id')->unsigned();
            $table->bigInteger('store_id')->unsigned();
            $table->bigInteger('user_created')->unsigned();
            $table->bigInteger('user_updated')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('user_created')->references('id')->on('users');
            $table->foreign('user_updated')->references('id')->on('users');
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
