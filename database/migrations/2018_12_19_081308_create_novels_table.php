<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNovelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('novels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 80)->comment('小说名称');
            $table->integer('novel_categories_id')->comment('书籍分类id');
            $table->integer('words_count')->comment('小说字数')->default(0);
            $table->integer('read_person_count')->comment('阅读人数')->default(0);
            $table->decimal('sale_amount', 10, 2)->comment('销售收入')->default(0);
            $table->boolean('wether_complete')->comment('是否完结')->default(false);
            $table->integer('read_count')->comment('阅读数量')->default(0);
            $table->integer('click_count')->comment('点击量')->default(0);
            $table->decimal('score', 10, 2)->comment('评分')->default(0);
            $table->integer('score_person_count')->comment('评价人数')->default(0);
            $table->integer('stored_count')->comment('被收藏数')->default(0);
            $table->integer('download_count')->comment('被下载数')->default(0);
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
        Schema::dropIfExists('novels');
    }
}
