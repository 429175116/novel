<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNovelWeeks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('novel_weeks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('novel_id')->comment('小说id');
            $table->string('week', 20)->comment('第几个周');
            $table->string('year', 20)->comment('年份');
            $table->integer('read_count')->comment('阅读数')->default(0);
            $table->integer('click_count')->comment('点击数')->default(0);
            $table->decimal('sale_amount', 10, 2)->comment('销售收入')->default(0);
            $table->integer('store_count')->comment('收藏数量')->default(0);

            $table->integer('month_ticket_count')->comment('月票数')->default(0);
            $table->decimal('month_ticket_toal_amount', 10, 2)->comment('月票收入')->default(0);
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
        //
    }
}
