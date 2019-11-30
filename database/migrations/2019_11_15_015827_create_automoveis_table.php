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
            $table->bigIncrements('NCODAUTO');
            $table->string('NTIPOAUTO', 20);
            $table->integer('NCODMARCA');
            $table->string('CNOMEMARCA', 500);
            $table->integer('NCODMODELO');
            $table->string('CNOMEMODELO', 500);
            $table->string('NANO', 10);
            $table->integer('CNOMEANO');
            $table->float('NVALOR', 8, 2);
            $table->string('CCOR', 20);
            $table->tinyInteger('NUNICODONO');
            $table->tinyInteger('NACEITATROCA');
            $table->string('NPLACA', 8);
            $table->tinyInteger('NFINALPLACA');
            $table->integer('NKMS')->default(0);
            $table->string('NCOMBUSTIVEL');
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
        Schema::dropIfExists('automoveis');
    }
}
