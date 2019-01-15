<?php
namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Model\NovelCategory;

class NovelCategoryTransformer extends TransformerAbstract{

    public function transform(NovelCategory $item){
        return [
            'id' => $item->id,
            'name' => $item->name,
            'img' => $item->img,
            'click_count' => $item->click_count,
            'total_amount' => $item->total_amount,
            'comment_count' => $item->comment_count,
            'created_at' =>$item->created_at->toDateTimeString(),
            'updated_at' =>$item->updated_at->toDateTimeString()
        ];
    }

    /**
     * 二级分类
     * @return \Closure
     */
    public static function forSecond() {
        return function (NovelCategory $item){
            return [
                'parent' => NovelCategory::query()->where('pid', $item->pid)->first(),
                'id' => $item->id,
                'name' => $item->name,
                'click_count' => $item->click_count,
                'total_amount' => $item->total_amount,
                'comment_count' => $item->comment_count,
                'created_at' =>$item->created_at->toDateTimeString(),
                'updated_at' =>$item->updated_at->toDateTimeString()
            ];
        };
    }

    /**
     * 二级分类
     * @return \Closure
     */
    public static function forHaveChildren() {
        return function (NovelCategory $item){
            return [
                'value' => $item->id,
                'label' => $item->name,
                'children' => NovelCategoryTransformer::dellChildren(NovelCategory::query()->where('pid', $item->id)->get()),
            ];
        };
    }

    private static function dellChildren($categories) {
        $data = [
        ];
        foreach ($categories as $category) {
            $tmp = [
                'value' => $category->id,
                'label' => $category->name
            ];
            array_push($data, $tmp);
        }
        return $data;
    }

}