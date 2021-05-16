<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPageHomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_page_homes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('page_id')->unsigned();
            $table->integer('order');
            $table->bigInteger('user_insert')->unsigned()->nullable();
            $table->bigInteger('user_update')->unsigned()->nullable();

            $table->foreign('user_insert')->references('id')->on('users');
            $table->foreign('user_update')->references('id')->on('users');
            $table->foreign('page_id')->references('id')->on('control_page_homes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_page_homes');
    }
}
