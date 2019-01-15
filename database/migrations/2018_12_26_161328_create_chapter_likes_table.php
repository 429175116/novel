<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChapterLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapter_likes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('comment_id')->commet('章节评论表id');
            $table->integer('user_id')->commet('用户id');
            $table->integer('chapter_id')->commet('章节id');
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
        Schema::dropIfExists('chapter_likes');
    }
}
