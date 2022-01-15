<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStoreCompanyColorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('colors_auto', function (Blueprint $table) {
            $table->bigInteger('store_id')->nullable()->after('user_update')->unsigned();
            $table->boolean('active')->after('store_id')->default(1);
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('colors_auto', function (Blueprint $table) {
            $table->dropForeign('colors_auto_store_id_foreign');
            $table->dropColumn('store_id');
            $table->dropColumn('active');
        });
    }
}
