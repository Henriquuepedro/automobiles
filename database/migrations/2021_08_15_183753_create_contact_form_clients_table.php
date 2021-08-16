<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactFormClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_form_clients', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("email");
            $table->string("subject");
            $table->string("phone");
            $table->text("message");
            $table->ipAddress("ip");
            $table->bigInteger('company_id')->unsigned();
            $table->bigInteger('store_id')->unsigned();
            $table->boolean('sended')->default(0);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('store_id')->references('id')->on('stores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_form_clients');
    }
}
