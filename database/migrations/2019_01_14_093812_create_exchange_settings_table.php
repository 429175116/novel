<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangeSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('amount',10,2)->comment('兑换金额');
            $table->integer('bi_count')->comment('兑换币数');
            $table->integer('bi_gift_count')->comment('赠送币数');
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
        Schema::dropIfExists('exchange_settings');
    }
}
