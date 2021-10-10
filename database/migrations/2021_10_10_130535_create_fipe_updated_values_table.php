<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFipeUpdatedValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fipe_updated_values', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('auto_fipe_id')->unsigned();
            $table->decimal('new_value', 12, 2);
            $table->decimal('old_value', 12, 2);
            $table->date('date_updated');
            $table->timestamps();

            $table->foreign('auto_fipe_id')->references('id')->on('fipe_autos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fipe_updated_values');
    }
}
