<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MonthTicket extends Model
{
    //
    protected $table = 'month_tickets';

    protected $fillable = [
        'price'
    ];
}
