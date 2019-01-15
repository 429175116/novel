<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class NovelMonthTicket extends Model
{
    //
    protected $table = 'novel_month_tickets';

    protected $fillable = [
        'novel_id', 'month', 'month_tickets_count', 'month_tickets_total_amount'
    ];

    public function novel() {
        return $this->belongsTo('App\Model\Novel');
    }
}
