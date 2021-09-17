<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateExpirationCompaniesStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dateTime('plan_expiration_date')->after('contact_secondary_phone');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->dateTime('plan_expiration_date')->after('color_layout_secondary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('plan_expiration_date');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('plan_expiration_date');
        });
    }
}
