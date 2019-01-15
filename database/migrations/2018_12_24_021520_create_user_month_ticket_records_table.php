<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMonthTicketRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_month_ticket_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户Id');
            $table->integer('month_ticket_count')->comment('购买月票张数');
            $table->decimal('month_ticket_total_amount', 10, 2)->comment('月票总金额');
            $table->integer('purchase_time')->comment('购买时间');
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
        Schema::dropIfExists('user_month_ticket_records');
    }
}
