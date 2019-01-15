<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class NovelCategory extends Model
{
    public static $rules = [
        'name' => 'required|unique:novel_categories',
    ];

    public static $message = [
        'name.required'  => '小说分类不得为空',
        'name.unique' => '小说分类重名'
    ];

    //
    protected $table = 'novel_categories';

    protected $fillable = [
        'name', 'pid', 'click_count', 'total_amount', 'comment_count', 'type'
    ];

    public function scopeId($query, $id) {
        return $query->where('id', $id);
    }

    public function scopePid($query, $pid) {
        return $query->where('pid', $pid);
    }

    public function scopeFirstCategory($query) {
        return $query->where('pid', 0);
    }
}
