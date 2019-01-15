<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Model\ExchangeSetting;

class ExchangeSettingTransformer extends TransformerAbstract{

    public function transform(ExchangeSetting $item){
        return [
            'id' => $item->id,
            'amount' => $item->amount,
            'bi_count' => $item->bi_count,
            'bi_gift_count' => $item->bi_gift_count
        ];
    }
}