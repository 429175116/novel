<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Author extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'authors';

    protected $fillable = [
        'user_id', 'wether_sign', 'level', 'account', 'password',
        'real_name', 'pen_name', 'id_number', 'bank_number', 'phone_number',
        'profile', 'wether_pass', 'again_request_status'
    ];

    public static $rules = [
        'real_name' => 'required',
        'pen_name' => 'required',
        'account' => 'required|unique:authors',
        'password' => 'required',
        'id_number' => 'identitycards|unique:authors',
        'profile' => 'required',
        'phone_number' => 'max:12'
    ];

    public static $message = [
        'real_name.required'  => '真实姓名不得为空',
        'pen_name.required' => '逼名必填',
        'account.required' => '账号必填',
        'account.unique' => '账号已重复',
        'password.required' => '密码必填',
        'id_number.identitycards' => '身份证号错误',
        'profile.required' => '头像必须上传',
        'phone_number.max' => '手机号不得大于12位'
    ];

    public function user() {
        return $this->belongsTo('App\Model\User');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        // TODO: Implement getJWTIdentifier() method.
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        // TODO: Implement getJWTCustomClaims() method.
        return [];
    }
}
