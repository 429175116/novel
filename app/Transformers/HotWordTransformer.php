<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Model\HotWord;

class HotWordTransformer extends TransformerAbstract{

    public function transform(HotWord $item){
        return [
            'id' => $item->id,
            'words' => $item->words,
            'word_count' => $item->word_count
        ];
    }

}