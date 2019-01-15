<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public static $rules = [
        'profile' => 'required',
        'nick_name' => 'required'
    ];

    public static $message = [
        'profile.required'  => '头像不得为空',
        'nick_name.required' => '名字这玩意必填'
    ];
    //
    protected $table = 'users';

    public function friend() {
        return $this->hasManyThrough('App\Model\User', 'App\Model\UserHasUser', 'from_id', 'id', 'id', 'to_id');
    }

    protected $fillable = [
        'open_id', 'profile', 'nick_name', 'level', 'wether_author'
    ];

    public function scopeOpenId($query, $open_id) {
        return $query->where('open_id', $open_id);
    }

    public function scopeNickName($query, $nick_name) {
        if(!empty($nick_name)){
            return $query->where('nick_name','like', '%'.$nick_name.'%');
        }
        return $query;
    }
}
