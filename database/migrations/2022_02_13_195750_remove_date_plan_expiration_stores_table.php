<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class RemoveDatePlanExpirationStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('plan_expiration_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fipe_autos', function (Blueprint $table) {
            $table->date('plan_expiration_date')->default(Carbon::now())->after('color_layout_secondary');
        });
    }
}
