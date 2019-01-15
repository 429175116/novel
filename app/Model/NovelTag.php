<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class NovelTag extends Model
{
    //
    protected $table = 'novel_tags';

    protected $fillable = [
        'novel_id', 'name'
    ];
}
