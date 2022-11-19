<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationToStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_to_stores', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('app_id')->unsigned();
            $table->tinyInteger('active');
            $table->bigInteger('store_id')->unsigned();
            $table->bigInteger('company_id')->unsigned();
            $table->timestamps();

            $table->foreign('app_id')->references('id')->on('applications');
            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('application_to_stores');
    }
}
