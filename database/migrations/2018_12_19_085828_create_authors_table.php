<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户Id');
            $table->boolean('wether_sign')->default(false);
            $table->tinyInteger('level')->default(0)->comment('作家等级');
            $table->string('real_name')->comment('真实姓名');
            $table->string('pen_name')->comment('逼名');
            $table->string('id_number', 40)->comment('身份证号码')->nullable();
            $table->string('bank_number', 40)->comment('银行卡')->nullable();
            $table->string('phone_number', 40)->comment('电话号码')->nullable();
            $table->string('profile')->comment('照片')->nullable();
            $table->boolean('wether_pass')->comment('作家请求是否通过')->default(false);
            $table->string('again_request_status', 20)->comment('再次请求状态')->default('requesting');
            $table->string('account', 40)->comment('登录后台用户名');
            $table->string('password')->comment('登录后台用户密码');
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
        Schema::dropIfExists('authors');
    }
}
