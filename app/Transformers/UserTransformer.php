<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Model\User;

class UserTransformer extends TransformerAbstract{

    public function transform(User $item){
        return [
            'id' => $item->id,
            'nick_name' => $item->nick_name,
            'profile' => $item->profile,
            'open_id' => $item->open_id
        ];
    }

    public static function forMyFriend() {
        return function (User $item){
            return [
                'id' => $item->id,
                'nick_name' => $item->nick_name,
                'profile' => $item->profile,
                'open_id' => $item->open_id
            ];
        };
    }

}