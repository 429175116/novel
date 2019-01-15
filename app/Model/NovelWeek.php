<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class NovelWeek extends Model
{
    //
    protected $table = 'novel_weeks';

    protected $fillable = [
        'novel_id', 'week', 'year', 'read_count', 'click_count', 'sale_amount',
        'store_count', 'category_id', 'month_ticket_count', 'month_ticket_toal_amount'
    ];

    public function novel() {
        return $this->belongsTo('App\Model\Novel');
    }

    public function scopeThisWeekNovel($query, $week, $novel_id) {
        return $query->where('week', $week)->where('novel_id', $novel_id);
    }

}
