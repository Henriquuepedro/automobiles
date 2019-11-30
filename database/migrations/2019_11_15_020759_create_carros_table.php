<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carros', function (Blueprint $table) {
            $table->bigIncrements('NCODCARRO');
            $table->integer('NCODAUTO')->references('NCODAUTO')->on('automoveis')->onDelete('cascade');
            $table->string('NCAMBIO', 20);
            $table->string('NDIRECAO', 20);
            $table->string('NMOTOR', 10);
            $table->string('NTIPOCARRO', 20);
            $table->integer('NPORTAS')->default(null);
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
        Schema::dropIfExists('carros');
    }
}
