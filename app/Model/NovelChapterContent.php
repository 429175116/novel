<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class NovelChapterContent extends Model
{
    //
    protected $table = 'novel_chapter_contents';

    protected $fillable = [
        'novel_chapter_id', 'content'
    ];
}
