<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteStoreidPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropForeign('plans_store_id_foreign');
            $table->dropColumn('store_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('opcional', function (Blueprint $table) {
            $table->bigInteger('store_id')->unsigned()->after('company_id');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });
    }
}
