<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefObsAutomoveisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('automoveis', function (Blueprint $table) {
            $table->string('reference')->after('code_auto_fipe')->nullable();
            $table->longText('observation')->after('reference')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('automoveis', function (Blueprint $table) {
            $table->dropColumn('reference');
            $table->dropColumn('observation');
        });
    }
}
