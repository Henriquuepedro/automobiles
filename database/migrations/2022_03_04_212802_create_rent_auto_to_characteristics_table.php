<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentAutoToCharacteristicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rent_auto_to_characteristics', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('auto_id')->unsigned();
            $table->bigInteger('characteristic_id')->unsigned();
            $table->timestamps();

            $table->foreign('auto_id')->references('id')->on('rent_automobiles');
            $table->foreign('characteristic_id')->references('id')->on('rent_characteristics');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rent_auto_to_characteristics');
    }
}
