<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Model\NovelMonth;

class NovelMonthTransformer extends TransformerAbstract{

    public function transform(NovelMonth $item){
        return [
            'novel' => $item->novel()->first(),
            'id' => $item->id,
            'month' => $item->month,
            'year' => $item->year,
            'read_count' => $item->read_count,
            'click_count' => $item->click_count,
            'sale_amount' => $item->sale_amount,
            'store_count' => $item->store_count,
            'month_ticket_count' => $item->month_ticket_count,
            'month_ticket_toal_amount' => $item->month_ticket_toal_amount,
        ];
    }
}