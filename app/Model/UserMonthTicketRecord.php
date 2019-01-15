<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserMonthTicketRecord extends Model
{
    //
    protected $table = 'user_month_ticket_records';

    protected $fillable = [
        'user_id', 'month_ticket_count', 'month_ticket_total_amount', 'purchase_time'
    ];
}
