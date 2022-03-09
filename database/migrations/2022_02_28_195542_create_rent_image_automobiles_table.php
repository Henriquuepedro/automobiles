<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentImageAutomobilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rent_image_automobiles', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->bigInteger('id')->unsigned();
            $table->bigInteger('auto_id')->unsigned();
            $table->string('file', 255);
            $table->string('folder', 255);
            $table->tinyInteger('primary');
            $table->timestamps();

            $table->foreign('auto_id')->references('id')->on('rent_automobiles')->onDelete('cascade');
            $table->primary(['auto_id', 'id']);

        });
        DB::statement('ALTER TABLE rent_image_automobiles MODIFY id INTEGER NOT NULL AUTO_INCREMENT');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rent_image_automobiles');
    }
}
