<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Model\NovelChapter;
use App\Enums\WetherVip;

class NovelChapterTransformer extends TransformerAbstract{

    public function transform(NovelChapter $item){
        return [
            'id' => $item->id,
            'title' => $item->title,
            'click_count' => $item->click_count,
            'sale_amount' => $item->sale_amount,
            'words_count' => $item->words_count,
            'wether_vip' => WetherVip::getKey($item->wether_vip),
            'content' => $item->content()->first(),
            'created_at' => !empty($item->created_at) ? $item->created_at->toDateTimeString(): '',
            'updated_at' => !empty($item->updated_at) ? $item->updated_at->toDateTimeString(): ''
        ];
    }
}