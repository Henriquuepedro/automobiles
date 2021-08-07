<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('company_id')->unsigned();

            $table->string('store_fancy', 256);
            $table->string('store_name', 256);
            $table->string('store_logo', 256)->nullable();
            $table->string('store_document_primary', 14);
            $table->string('store_document_secondary', 32)->nullable();
            $table->string('type_store', 2);

            $table->string('store_domain', 256)->nullable();
            $table->string('store_without_domain', 256)->nullable();
            $table->tinyInteger('type_domain')->comment('0=sem dominio | 1=dominio proprio');

            $table->longText('store_about')->nullable();

            $table->string('mail_contact_email', 256)->nullable();
            $table->string('mail_contact_password', 256)->nullable();
            $table->string('mail_contact_smtp', 256)->nullable();
            $table->string('mail_contact_port', 256)->nullable();
            $table->string('mail_contact_security', 256)->nullable();

            $table->string('contact_email', 256)->nullable();
            $table->string('contact_primary_phone', 11)->nullable();
            $table->string('contact_secondary_phone', 11)->nullable();
            $table->tinyInteger('contact_primary_phone_have_whatsapp')->default(0);
            $table->tinyInteger('contact_secondary_phone_have_whatsapp')->default(0);

            $table->text('social_networks')->nullable();

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

            $table->string('color_layout_primary', 7)->default('#000');
            $table->string('color_layout_secondary', 7)->default('#666');

            $table->bigInteger('user_updated')->nullable()->unsigned();

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies');
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
        Schema::dropIfExists('stores');
    }
}
