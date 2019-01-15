<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Model\Novel;
use App\Enums\WetherVip;
use App\Enums\NovelWetherComplete;

class NovelTransformer extends TransformerAbstract{

    public function transform(Novel $item){
        return [
            'id' => $item->id,
            'name' => $item->name,
            'img' => $item->img,
            'words_count' => $item->words_count,
            'read_person_count' => $item->read_person_count,
            'sale_amount' => $item->sale_amount,
            'wether_complete' => NovelWetherComplete::getKey($item->wether_complete),
            'read_count' => $item->read_count,
            'click_count' => $item->click_count,
            'score' => $item->score,
            'score_person_count' => $item->score_person_count,
            'stored_count' => $item->stored_count,
            'download_count' => $item->download_count,
            'created_at' => !empty($item->created_at) ? $item->created_at->toDateTimeString(): '',
            'updated_at' => !empty($item->updated_at) ? $item->updated_at->toDateTimeString(): ''
        ];
    }

    public static function forDetail() {
        return function (Novel $item){
            return [
                'id' => $item->id,
                'name' => $item->name,
                'img' => $item->img,
                'intro' => $item->intro,
                'words_count' => $item->words_count,
                'read_person_count' => $item->read_person_count,
                'sale_amount' => $item->sale_amount,
                'wether_complete' => NovelWetherComplete::getKey($item->wether_complete),
                'read_count' => $item->read_count,
                'click_count' => $item->click_count,
                'score' => $item->score,
                'score_person_count' => $item->score_person_count,
                'stored_count' => $item->stored_count,
                'download_count' => $item->download_count,
                'created_at' => !empty($item->created_at) ? $item->created_at->toDateTimeString(): '',
                'updated_at' => !empty($item->updated_at) ? $item->updated_at->toDateTimeString(): '',
                'chapters' => NovelTransformer::dellChapters($item->chapter()->get())
            ];
        };
    }

    public static function forRolling() {
        return function (Novel $item){
            return [
                'id' => $item->id,
                'img' => $item->img
            ];
        };
    }

    private static function dellChapters($chapters) {
        $data = [
        ];
        foreach ($chapters as $chapter) {
            $tmp = [
                'id' => $chapter->id,
                'title' => $chapter->title,
                'click_count' => $chapter->click_count,
                'sale_amount' => $chapter->sale_amount,
                'words_count' => $chapter->words_count,
                'wether_vip' => WetherVip::getKey($chapter->wether_vip),
            ];
            array_push($data, $tmp);
        }
        return $data;
    }


}