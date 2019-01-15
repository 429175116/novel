<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserHasUser extends Model
{
    //
    protected $table = 'user_has_users';
    protected $fillable = [
        'from_id', 'to_id'
    ];
}
