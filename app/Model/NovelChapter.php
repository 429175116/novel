<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class NovelChapter extends Model
{
    //
    protected $table = 'novel_chapters';

    protected $fillable = [
        'novel_id', 'title', 'click_count', 'sale_amount', 'words_count',
        'wether_vip'
    ];

    public static $rules = [
        'title' => 'required',
        'content' => 'required'
    ];

    public static $message = [
        'title.required'  => '章节标题不得为空',
        'content.required' => '章节内容不得为空'
    ];

    public function content() {
        return $this->hasOne('App\Model\NovelChapterContent', 'novel_chapter_id', 'id');
    }
}
