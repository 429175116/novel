<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Model\NovelWeek;

class NovelWeekTransformer extends TransformerAbstract{

    public function transform(NovelWeek $item){
        return [
            'novel' => $item->novel()->first(),
            'id' => $item->id,
            'year' => $item->year,
            'read_count' => $item->read_count,
            'click_count' => $item->click_count,
            'sale_amount' => $item->sale_amount,
            'store_count' => $item->store_count,
        ];
    }
}