<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplementarAutoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complementar_auto', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('auto_id')->unsigned();
            $table->string('valores')->comment('JSON com os ids das informações complementares do automóvel');
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
        Schema::dropIfExists('complementar_auto');
    }
}
