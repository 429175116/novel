<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNovelMonthTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('novel_month_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('novel_id')->comment('小说id');
            $table->string('month', 20)->comment('月份');
            $table->integer('month_tickets_count')->comment('月票数')->default(0);
            $table->decimal('month_tickets_total_amount')->comment('月票收入')->default(0);
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
        Schema::dropIfExists('novel_month_tickets');
    }
}
