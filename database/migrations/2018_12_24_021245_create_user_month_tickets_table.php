<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMonthTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_month_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户Id');
            $table->integer('month_ticket_total_count')->comment('总月票数')->default(0);
            $table->decimal('month_ticket_total_amount', 10, 2)->comment('金额')->default(0);
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
        Schema::dropIfExists('user_month_tickets');
    }
}
