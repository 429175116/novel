<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Model\NovelDay;

class NovelDayTransformer extends TransformerAbstract{

    public function transform(NovelDay $item){
        return [
            'novel' => $item->novel()->first(),
            'id' => $item->id,
            'date' => $item->date,
            'read_count' => $item->read_count,
            'click_count' => $item->click_count,
            'sale_amount' => $item->sale_amount,
            'store_count' => $item->store_count,
        ];
    }
}