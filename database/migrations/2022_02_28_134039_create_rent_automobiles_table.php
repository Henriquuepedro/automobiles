<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentAutomobilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rent_automobiles', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_auto', 20);
            $table->string('folder_images');
            $table->bigInteger('color')->unsigned()->nullable();
            $table->string('license', 8);
            $table->integer('kilometers')->default(0);
            $table->boolean('featured');
            $table->bigInteger('code_auto_fipe')->unsigned();
            $table->string('reference')->nullable();
            $table->longText('observation')->nullable();
            $table->boolean('active');
            $table->bigInteger('fuel')->unsigned();
            $table->bigInteger('company_id')->unsigned();
            $table->bigInteger('store_id')->unsigned();
            $table->bigInteger('user_created')->unsigned();
            $table->bigInteger('user_updated')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('user_created')->references('id')->on('users');
            $table->foreign('user_updated')->references('id')->on('users');
            $table->foreign('code_auto_fipe')->references('id')->on('fipe_autos');
            $table->foreign('color')->references('id')->on('colors_auto')->onDelete('cascade');
            $table->foreign('fuel')->references('id')->on('fuel_autos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rent_automobiles');
    }
}
