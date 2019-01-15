<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ChapterLike extends Model
{
    protected $table = "chapter_likes";

    protected $fillable = [
        'comment_id', 'user_id', 'chapter_id'
    ];
}
