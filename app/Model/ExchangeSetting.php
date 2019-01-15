<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ExchangeSetting extends Model
{
    //
    protected $table = "exchange_settings";

    protected $fillable = [
        'amount', 'bi_count', 'bi_gift_count'
    ];
}
