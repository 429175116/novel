<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Model\Author;

class AuthorTransformer extends TransformerAbstract{

    public function transform(Author $item){
        return [
            'id' => $item->id,
            'real_name' => $item->real_name,
            'pen_name' => $item->pen_name,
            'user' => $item->user()->first()
        ];
    }

}