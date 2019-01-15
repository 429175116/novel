<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserStore extends Model
{
    //
    protected $table = 'user_stores';

    protected $fillable = [
        'user_id', 'novel_id'
    ];

    public function scopeUserId($query, $user_id) {
        return $query->where('user_id', $user_id);
    }

    public function scopeNovelId($query, $novel_id) {
        return $query->where('novel_id', $novel_id);
    }

    public function novel() {
        return $this->hasOne('App\Model\Novel', 'id', 'novel_id');
    }
}
