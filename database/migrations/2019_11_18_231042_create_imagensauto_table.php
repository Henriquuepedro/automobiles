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
            $table->integer('NCODIMAGES')->unsigned();
            $table->integer('NCODAUTO')->references('NCODAUTO')->on('automoveis')->onDelete('cascade');
            $table->primary(['NCODAUTO', 'NCODIMAGES']);
            $table->string('PATH', 255);
            $table->tinyInteger('PRIMARY');
            $table->timestamps();

        });
        DB::statement('ALTER TABLE imagensauto MODIFY NCODIMAGES INTEGER NOT NULL AUTO_INCREMENT');
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
