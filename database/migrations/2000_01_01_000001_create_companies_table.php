<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            $table->string('company_fancy', 256);
            $table->string('company_name', 256);

            $table->string('company_logo', 256)->nullable();

            $table->string('company_document_primary', 14);
            $table->string('company_document_secondary', 32)->nullable();

            $table->string('type_company', 2);

            $table->string('contact_email', 256)->nullable();
            $table->string('contact_primary_phone', 11)->nullable();
            $table->string('contact_secondary_phone', 11)->nullable();

            $table->bigInteger('user_updated')->nullable()->unsigned();
            $table->bigInteger('user_created')->unsigned();

            $table->timestamps();

            $table->foreign('user_updated')->references('id')->on('users');
            $table->foreign('user_created')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
