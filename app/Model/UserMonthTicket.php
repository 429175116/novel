<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserMonthTicket extends Model
{
    //
    protected $table = 'user_month_tickets';

    protected $fillable = [
        'user_id', 'month_ticket_total_count', 'month_ticket_total_amount'
    ];
}
