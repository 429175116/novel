<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Novel extends Model
{
    //
    protected $table = 'novels';

    protected $fillable = [
        'name', 'novel_categories_id', 'words_count', 'read_person_count', 'sale_amount',
        'wether_complete', 'read_count', 'click_count', 'score', 'score_person_count',
        'stored_count', 'download_count', 'author_id', 'img', 'wether_free'
    ];

    public static $rules = [
        'name' => 'required|unique:novels',
        'novel_categories_id' => 'required',
    ];

    public static $message = [
        'name.required'  => '小说名称不得为空',
        'name.unique' => '此书名已存在',
        'novel_categories_id.required' => '小说分类必填'
    ];


    public function chapter() {
        return $this->hasMany('App\Model\NovelChapter', 'novel_id', 'id');
    }

    public function scopeName($query, $name) {
        if(!empty($name)) {
            return $query->where('name', 'like', '%'.$name.'%');
        }
        return $query;
    }

    public function scopeCompelete($query) {
        return $query->where('wether_complete', true);
    }

    public function scopeWhetherCompelete($query, $whether_compelete) {
        if(!empty($whether_compelete)) {
            return $query->where('wether_complete', $whether_compelete);
        }
        return $query;
    }


    public function scopeCategoryId($query, $categories_id) {
        return $query->where('novel_categories_id', $categories_id);
    }

    public function scopeWhereCateGoryIn($query, $categories_ids) {
        return $query->whereIn('novel_categories_id', $categories_ids);
    }


    public function scopeOrderByProp($query, $prop = 'click_count', $sort = 'desc') {
        if(!empty($prop)) {
            if(empty($sort)) {
               return $query->orderby($prop, 'desc');
            }
            return $query->orderby($prop, $sort);
        }
        if(empty($sort)) {
            return $query->orderby('click_count', 'desc');
        }
        return $query->orderby('click_count', $sort);
    }
}
