<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class HotWord extends Model
{
    //
    protected $table = 'hot_words';

    protected $fillable = [
        'words', 'word_count'
    ];
}
