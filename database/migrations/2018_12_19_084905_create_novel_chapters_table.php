<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNovelChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('novel_chapters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('novel_id')->comment('小说Id');
            $table->string('title', 100)->comment('标题');
            $table->integer('click_count')->comment('点击数')->default(0);
            $table->decimal('sale_amount', 10, 2)->comment('销售收入')->default(0);
            $table->integer('words_count')->comment('字数')->default(0);
            $table->boolean('wether_vip')->comment('是否是vip章节')->default(false);
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
        Schema::dropIfExists('novel_chapters');
    }
}
