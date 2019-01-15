<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNovelDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('novel_days', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('novel_id')->comment('小说id');
            $table->string('date', 20)->comment('日期');
            $table->integer('read_count')->comment('阅读数')->default(0);
            $table->integer('click_count')->comment('点击数')->default(0);
            $table->decimal('sale_amount', 10, 2)->comment('收入')->default(0);
            $table->integer('store_count')->comment('收藏数')->default(0);

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
        Schema::dropIfExists('novel_days');
    }
}
