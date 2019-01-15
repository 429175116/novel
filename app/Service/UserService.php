<?php
namespace App\Service;
use App\Model\User;
use App\Traits\ApiResponse;

class UserService
{

    //获取用户
    public static function getUser($openid) {
        $user = User::query()->where('open_id', $openid)->first();
        if(empty($user)){
            return [
                'errcode' => 404,
                'errmsg' => '未找到相关用户',
                'data' => []
            ];
        }
        return [
            'errcode' => 0,
            'errmsg' => '',
            'data' => $user
        ];

    }

}