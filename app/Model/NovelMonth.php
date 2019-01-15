<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class NovelMonth extends Model
{
    //
    protected $table = 'novel_months';

    protected $fillable = [
        'novel_id', 'month', 'year', 'read_count', 'click_count', 'sale_amount',
        'store_count', 'category_id', 'month_ticket_count', 'month_ticket_toal_amount'
    ];

    public function scopeMonth($query, $month) {
        return $query->where('month', $month);
    }

    public function scopeNovelId($query, $novel_id) {
        return $query->where('novel_id', $novel_id);
    }

    public function scopeThisMonthNovel($query, $month, $novel_id) {
        return $query->where('month', $month)->where('novel_id', $novel_id);
    }

    public function novel() {
        return $this->belongsTo('App\Model\Novel');
    }
}
