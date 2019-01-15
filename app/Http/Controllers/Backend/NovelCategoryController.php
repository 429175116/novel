<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\NovelCategory;

use App\Transformers\NovelCategoryTransformer;

class NovelCategoryController extends Controller
{
    //
    /**
     * 添加一级分类
     * @param Request $request
     */
    public function createFirstCategory(Request $request) {
        if(!empty($errors = $this->myValidator($request, NovelCategory::$rules, NovelCategory::$message))) return $errors;//验证
        $input = $request->all();
        $novel_category = new NovelCategory();
        $novel_category->name = $input['name'];
        $novel_category->save();
        return $this->apiSuccess();
    }

    public function firstCategory() {
        $first_category = NovelCategory::query()->where('pid', 0)->get();
        $data = [
            'first_categories' => $this->fractalItems($first_category, new NovelCategoryTransformer())
        ];
        return $this->apiSuccess($data);
    }


    public function createSecondCategory(Request $request, NovelCategory $parent) {
        if(!empty($errors = $this->myValidator($request, NovelCategory::$rules, NovelCategory::$message))) return $errors;//验证
        $input = $request->all();
        $novel_category = new NovelCategory();
        $novel_category->name = $input['name'];
        $novel_category->img = $input['img'];
        $novel_category->pid = $parent->id;
        $novel_category->save();
        return $this->apiSuccess();
    }

    public function secondCategory(NovelCategory $parent) {
        $first_category = NovelCategory::query()->where('pid', $parent->id)->get();
        $data = [
            'first_categories' => $this->fractalItems($first_category, new NovelCategoryTransformer())
        ];
        return $this->apiSuccess($data);
    }

    public function categoryForCascader() {
        $novel_categories = NovelCategory::firstCategory()->get();
        $data = [
            'novel_categories' => $this->fractalItems($novel_categories, NovelCategoryTransformer::forHaveChildren())
        ];
        return $this->apiSuccess($data);
    }

}
