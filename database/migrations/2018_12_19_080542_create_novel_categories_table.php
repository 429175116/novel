<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNovelCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('novel_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20)->comment('分类名称');
            $table->tinyInteger('pid')->comment('父id')->default(0);
            $table->integer('click_count')->comment('点击数')->default(0);
            $table->decimal('total_amount', 10, 2)->comment('收入')->default(0);
            $table->integer('comment_count')->comment('评论数')->default(0);
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
        Schema::dropIfExists('novel_categories');
    }
}
