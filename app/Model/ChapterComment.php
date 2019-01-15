<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ChapterComment extends Model
{
    protected $table = "chapter_comments";

    protected $fillable = [
      'user_id', 'chapter_id', 'content', 'parent_id', 'likes_number'
    ];
}
