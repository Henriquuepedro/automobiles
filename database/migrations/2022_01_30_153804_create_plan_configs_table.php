<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('code');
            $table->decimal('amount');
            $table->integer('qty_months')->comment('mensal | trimestral | semestral | anual');
            $table->string('description');
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('primary')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_configs');
    }
}
