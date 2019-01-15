<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Model\UserStore;

class UserStoreTransformer extends TransformerAbstract{

    public function transform(UserStore $item){
        return [
            'id' => $item->id,
            'novel' => $item->novel()->first(),
        ];
    }
}