<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagensautoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imagensauto', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->integer('id')->unsigned();
            $table->bigInteger('auto_id')->unsigned();
            $table->string('arquivo', 255);
            $table->tinyInteger('primaria');
            $table->timestamps();

            $table->foreign('auto_id')->references('id')->on('automoveis')->onDelete('cascade');
            $table->primary(['auto_id', 'id']);

        });
        DB::statement('ALTER TABLE imagensauto MODIFY id INTEGER NOT NULL AUTO_INCREMENT');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('imagensauto');
    }
}
