<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Model\UserSignRecord;

class UserSignTransformer extends TransformerAbstract{

    private static $status = [
        'login' => '已登录',
        'signin' => '已签到'
    ];

    public function transform(UserSignRecord $item){
        return [
            'id' => $item->id,
            'date' => $item->date,
            'status' => UserSignTransformer::$status[$item->status],
            'continuous_day' => $item->continuous_day
        ];
    }

}