<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rent_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('visible_type')->default(0);
            $table->bigInteger('company_id')->unsigned();
            $table->bigInteger('store_id')->unsigned();
            $table->bigInteger('user_updated')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('user_updated')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rent_settings');
    }
}
