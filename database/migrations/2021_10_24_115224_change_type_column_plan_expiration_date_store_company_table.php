<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class ChangeTypeColumnPlanExpirationDateStoreCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->date('plan_expiration_date')->default(Carbon::now())->change();
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->date('plan_expiration_date')->default(Carbon::now())->change();
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
            $table->dateTime('plan_expiration_date')->change();
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->dateTime('plan_expiration_date')->change();
        });
    }
}
