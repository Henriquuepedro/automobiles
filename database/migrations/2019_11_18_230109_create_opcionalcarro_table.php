<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpcionalcarroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opcionalcarro', function (Blueprint $table) {
            $table->bigIncrements('NCODOPCIONALCARRO');
            $table->integer('NCODCARRO')->references('NCODCARRO')->on('carros')->onDelete('cascade');
            $table->tinyInteger('AIRBAG');
            $table->tinyInteger('ALARME');
            $table->tinyInteger('ARCONDICIONADO');
            $table->tinyInteger('TRAVAELETRICA');
            $table->tinyInteger('VIDROELETRICO');
            $table->tinyInteger('SOM');
            $table->tinyInteger('SENSORRE');
            $table->tinyInteger('CAMERARE');
            $table->tinyInteger('BLINDADO');
            $table->tinyInteger('DIRECAOHIDRAULICA');
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
        Schema::dropIfExists('opcionalcarro');
    }
}
