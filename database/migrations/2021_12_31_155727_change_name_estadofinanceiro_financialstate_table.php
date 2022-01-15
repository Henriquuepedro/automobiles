<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNameEstadofinanceiroFinancialstateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('estadofinanceiro', 'financial_state');
        Schema::rename('estados_financeiro', 'financial_states');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('financial_state', 'estadofinanceiro');
        Schema::rename('financial_states', 'estados_financeiro');
    }
}
