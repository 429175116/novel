<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AddFriendNotice extends Model
{
    //
    private $status = [
        'un_reaad' => '未读',
        'agree' => '同意',
        'disagree' => '拒绝'
    ];

    //
    protected $table = 'add_friend_notices';

    protected $fillable = [
        'from', 'to', 'status', 'message'
    ];

    public function requester() {
        return $this->hasOne('App\Model\User', 'id', 'from');
    }
}
