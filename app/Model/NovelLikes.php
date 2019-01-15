<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class NovelLikes extends Model
{
    protected $table = "novel_likes";

    protected $fillable = [
        'comment_id', 'user_id', 'novel_id'
    ];
}
