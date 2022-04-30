<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentPlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rent_places', function (Blueprint $table) {
            $table->id();

            $table->string('address_zipcode', 8)->nullable();
            $table->string('address_public_place', 256)->nullable();
            $table->string('address_number', 256)->nullable();
            $table->string('address_complement', 256)->nullable();
            $table->string('address_reference', 256)->nullable();
            $table->string('address_neighborhoods', 256)->nullable();
            $table->string('address_city', 256)->nullable();
            $table->string('address_state', 256)->nullable();
            $table->string('address_lat', 256)->nullable();
            $table->string('address_lng', 256)->nullable();

            $table->boolean('devolution');
            $table->boolean('withdrawal');

            $table->string('contact_email', 256)->nullable();
            $table->string('contact_primary_phone', 11)->nullable();
            $table->string('contact_secondary_phone', 11)->nullable();
            $table->tinyInteger('contact_primary_phone_have_whatsapp')->default(0);
            $table->tinyInteger('contact_secondary_phone_have_whatsapp')->default(0);

            $table->bigInteger('company_id')->unsigned();
            $table->bigInteger('store_id')->unsigned();

            $table->bigInteger('user_created')->unsigned();
            $table->bigInteger('user_updated')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('user_created')->references('id')->on('users');
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
        Schema::dropIfExists('rent_places');
    }
}
