<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserSubscriotion extends Model
{
    //
    protected $table = 'user_subscriptions';

    protected $fillable = [
        'user_id', 'novel_chapter_id', 'novel_id', 'category_id'
    ];
}
