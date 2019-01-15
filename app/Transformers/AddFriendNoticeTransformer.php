<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Model\AddFriendNotice;

class AddFriendNoticeTransformer extends TransformerAbstract{

    private static $status = [
        'un_reaad' => '未读',
        'agree' => '同意',
        'disagree' => '拒绝'
    ];

    public function transform(AddFriendNotice $item){
        return [
            'id' => $item->id,
            'from' => $item->requester()->first(),
            'status' => AddFriendNoticeTransformer::$status[$item->status],
            'message' => $item->message
        ];
    }

}