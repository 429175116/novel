<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class NovelReadUser extends Model
{
    //
    protected $table = 'novel_read_users';

    protected $fillable = [
        'novel_id', 'user_id'
    ];
}
