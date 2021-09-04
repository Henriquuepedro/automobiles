<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFipeAutosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fipe_autos', function (Blueprint $table) {
            $table->id();
            $table->string('type_auto');

            $table->decimal('value', 12, 2);
            $table->string('brand_name');
            $table->string('model_name');
            $table->string('year_name');
            $table->string('fuel');
            $table->string('code_fipe');
            $table->string('month_reference');
            $table->integer('type_auto_id');
            $table->string('initials_fuel', 4);

            $table->bigInteger('brand_id')->unsigned();
            $table->bigInteger('model_id')->unsigned();
            $table->bigInteger('year_id')->unsigned();
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('fipe_brands');
            $table->foreign('model_id')->references('id')->on('fipe_models');
            $table->foreign('year_id')->references('id')->on('fipe_years');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fipe_autos');
    }
}
