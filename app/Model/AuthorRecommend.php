<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AuthorRecommend extends Model
{
    //
    protected $table = 'author_recommends';

    protected $fillable = [
        'author_id', 'novel_id', 'category_id'
    ];
}
