<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class userSignRecord extends Model
{
    //
    private $status = [
        'login' => '已登录',
        'signin' => '已签到'
    ];

    protected $table = 'user_sign_records';

    protected $fillable = [
        'user_id', 'date', 'status', 'continuous_day'
    ];

    public function user() {
        return $this->belongsTo('App\Model\Users');
    }
}
