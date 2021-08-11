<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageDynamicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_dynamics', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('title');
            $table->longText('conteudo');
            $table->tinyInteger('ativo')->default(1);
            $table->bigInteger('company_id')->unsigned();
            $table->bigInteger('store_id')->unsigned();
            $table->bigInteger('user_insert')->unsigned();
            $table->bigInteger('user_update')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('store_id')->references('id')->on('stores');
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
        Schema::dropIfExists('page_dynamics');
    }
}
