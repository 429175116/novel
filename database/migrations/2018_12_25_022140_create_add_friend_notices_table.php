<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddFriendNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('add_friend_notices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('from')->comment('发送人');
            $table->integer('to')->comment('接收人');
            $table->string('status')->comment('状态')->default('un_read');
            $table->text('message')->comment('消息');
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
        Schema::dropIfExists('add_friend_notices');
    }
}
