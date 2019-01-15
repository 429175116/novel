<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class NovelComment extends Model
{
    protected $table = "novel_comments";

    protected $fillable = [
        'user_id', 'novel_id', 'content', 'parent_id', 'likes_number'
    ];
}
