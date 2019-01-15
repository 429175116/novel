<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class NovelDay extends Model
{
    //
    protected $table = 'novel_days';

    protected $fillable = [
        'novel_id', 'date', 'read_count', 'click_count', 'sale_amount',
        'store_count', 'category_id', 'month_ticket_count', 'month_ticket_toal_amount'
    ];

    public function novel() {
        return $this->belongsTo('App\Model\Novel');
    }

    public function scopeDate($query, $date) {
        return $query->where('date', $date);
    }

    public function scopeNovelId($query, $novel_id) {
        return $query->where('novel_id', $novel_id);
    }

    public function scopeTodayNovel($query, $date, $novel_id) {
        return $query->where('date', $date)->where('novel_id', $novel_id);
    }
}
