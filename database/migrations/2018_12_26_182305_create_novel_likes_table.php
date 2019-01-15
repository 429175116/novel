<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNovelLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('novel_likes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('comment_id')->commet('小说评论表id');
            $table->integer('user_id')->commet('用户id');
            $table->integer('novel_id')->commet('小说id');
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
        Schema::dropIfExists('novel_likes');
    }
}
