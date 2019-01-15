<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('open_id', 40)->comment('openid')->unique();
            $table->string('profile')->comment('头像');
            $table->string('nick_name', 80)->comment('昵称');
            $table->tinyInteger('level')->comment('会员等级')->default(0);
            $table->boolean('wether_author')->comment('是否为作者')->default(false);
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
        Schema::dropIfExists('users');
    }
}
