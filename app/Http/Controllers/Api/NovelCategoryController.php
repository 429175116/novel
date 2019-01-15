<?php

namespace App\Http\Controllers\Api;

use App\Model\Author;
use App\Model\AuthorRecommend;
use App\Model\Novel;
use App\Model\NovelMonth;
use App\Model\NovelMonthTicket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\NovelCategory;
use App\Transformers\NovelCategoryTransformer;
use App\Transformers\NovelTransformer;
use App\Transformers\NovelWeekTransformer;
use App\Transformers\NovelDayTransformer;
use App\Transformers\NovelMonthTransformer;
use App\Model\NovelWeek;
use App\Model\NovelDay;


class NovelCategoryController extends Controller
{
    /**
     * 一级分类获取
     * @return \Illuminate\Http\JsonResponse
     */
    public function firstCategory() {
        $first_category = NovelCategory::query()->where('pid', 0)->get();
        $data = [
            'first_categories' => $this->fractalItems($first_category, new NovelCategoryTransformer())
        ];
        return $this->apiSuccess($data);
    }

    /**
     * 小说二级分类
     * @param NovelCategory $parent
     * @return \Illuminate\Http\JsonResponse
     */
    public function secondCategory($id) {
        $parent = NovelCategory::query()->findorfail($id);
        $first_category = NovelCategory::query()->where('pid', $parent->id)->get();
        $data = [
            'parent' => $this->fractalItem($parent, new NovelCategoryTransformer()),
            'second_categories' => $this->fractalItems($first_category, new NovelCategoryTransformer())
        ];
        return $this->apiSuccess($data);
    }

    /**
     * 此大分类下的新书 也就是起点对应的 xx新书
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function newBooksOfFirstCategory($id) {
//        $find_second_categories = $this->_findSecondCateGoryIds($id);
//        if($find_second_categories['errcode'] > 0) return $this->apiError($find_second_categories['errmsg']);
//        $children_ids = $find_second_categories['data'];

        $novels = Novel::query()->where('novel_categories_id', $id)
                                ->orderby('id', 'desc')
                                ->limit(10)
                                ->get();
        $data = [
           'new_books' => $this->fractalItems($novels, new NovelTransformer())
        ];
        return $this->apiSuccess($data);
    }

    /**
     * 人气连载
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function popularUncompelete($id) {
//        $find_second_categories = $this->_findSecondCateGoryIds($id);
//        if($find_second_categories['errcode'] > 0) return $this->apiError($find_second_categories['errmsg']);
//        $children_ids = $find_second_categories['data'];

        $novels = Novel::query()->where('novel_categories_id', $id)
                                ->OrderByProp()
                                ->limit(20)
                                ->get();
        $data = [
            'popular_books' => $this->fractalItems($novels, new NovelTransformer())
        ];
        return $this->apiSuccess($data);
    }

    /**
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function bestSeller($id) {
//        $find_second_categories = $this->_findSecondCateGoryIds($id);
//        if($find_second_categories['errcode'] > 0) return $this->apiError($find_second_categories['errmsg']);
//        $children_ids = $find_second_categories['data'];
        $novels = Novel::query()->where('novel_categories_id', $id)
            ->OrderByProp('sale_amount', 'desc')
            ->limit(20)
            ->get();
        $data = [
            'compelete_boutique' => $this->fractalItems($novels, new NovelTransformer())
        ];
        return $this->apiSuccess($data);

    }


    /**
     * xx下的热门分类
     */
    public function popularCateGory($id) {
        $parent = NovelCategory::query()->findorfail($id);
        $poplar_category = NovelCategory::query()->pid($parent->id)->orderby('click_count', 'desc')->first();
        $novels = Novel::query()->categoryId($poplar_category->id)
                                ->orderby('click_count', 'desc')
                                ->limit(20)
                                ->get();
        $data = [
            'poplar_category' => $this->fractalItem($poplar_category, new NovelCategoryTransformer()),
            'popular_category_books' => $this->fractalItems($novels, new NovelTransformer())
        ];
        return $this->apiSuccess($data);
    }

    /**
     * 点击周榜
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function clickWeekBang($id) {
//        $find_second_categories = $this->_findSecondCateGoryIds($id);
//        if($find_second_categories['errcode'] > 0) return $this->apiError($find_second_categories['errmsg']);
//        $children_ids = $find_second_categories['data'];
        $this_week = date('W');
        $novel_week = NovelWeek::query()->where('category_id', $id)
                                        ->where('week', $this_week)
                                        ->orderby('click_count', 'desc')
                                        ->limit(20)->get();
        $data = [
            'click_week_bang' => $this->fractalItems($novel_week, new NovelWeekTransformer())
        ];
        return $this->apiSuccess($data);
    }

    /**
     * 24小时热销
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function twentyFourHourHotSale($id) {
        $find_second_categories = $this->_findSecondCateGoryIds($id);
        if($find_second_categories['errcode'] > 0) return $this->apiError($find_second_categories['errmsg']);
        $children_ids = $find_second_categories['data'];
        $today = date('Y-m-d');
        $novel_today = NovelDay::query()->whereIn('category_id', $children_ids)
                                        ->where('date', $today)
                                        ->orderby('click_count', 'desc')
                                        ->limit(20)->get();
        $data = [
            'twentyfourhour_hot_sale' => $this->fractalItem($novel_today, new NovelDayTransformer())
        ];
        return $this->apiSuccess($data);
    }

    /**
     * 点赞榜
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeBang($id) {
        $find_second_categories = $this->_findSecondCateGoryIds($id);
        if($find_second_categories['errcode'] > 0) return $this->apiError($find_second_categories['errmsg']);
        $children_ids = $find_second_categories['data'];
        $novels = Novel::query()->whereCateGoryIn($children_ids)
            ->compelete()
            ->OrderByProp()
            ->limit(20)
            ->get();
        $data = [
            'store_bang' => $this->fractalItems($novels, new NovelTransformer())
        ];
        return $this->apiSuccess($data);
    }


    /**
     * 完本精品
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function compeleteBoutique($id) {
        $find_second_categories = $this->_findSecondCateGoryIds($id);
        if($find_second_categories['errcode'] > 0) return $this->apiError($find_second_categories['errmsg']);
        $children_ids = $find_second_categories['data'];

        $novels = Novel::query()->whereCateGoryIn($children_ids)
                                ->compelete()
                                ->OrderByProp('')
                                ->limit(20)
                                ->get();
        $data = [
            'compelete_boutique' => $this->fractalItems($novels, new NovelTransformer())
        ];
        return $this->apiSuccess($data);
    }

    public function monthTicketBang($id) {//二级分类id

        $novel_month_tickets = NovelMonth::where('category_id', $id)->where('month', date('Y-m'))->orderBy('month_ticket_count', 'desc')->limit(10)->get();
        $data = [
            'novel_month_tickets' => $this->fractalItems($novel_month_tickets, new NovelMonthTransformer())
        ];
        return $this->apiSuccess($data);
    }

    //畅销榜
    public function bestSaleWithSecondCategory($id) {
        $novels = Novel::where('novel_categories_id', $id)->orderby('sale_amount', 'desc')->limit(10)->get();
        $data = [
            '$data' => $this->fractalItems($novels, new NovelTransformer())
        ];
        return $this->apiSuccess($data);
    }

    /**
     * 二级分类下的小说 带分页
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function findBookBySecondCategory(Request $request, $id) {
        $novel_category = NovelCategory::findorfail($id);
        $input = $request->all();
        $novels = Novel::query()->categoryId($id)
                                ->orderby('words_count', 'desc')
                                ->WhetherCompelete($input['wether_complete'])
                                ->OrderByProp()
                                ->paginate(20);
        $data = [
            'novel_category' => $this->fractalItem($novel_category, NovelCategoryTransformer::forSecond()),
            'novels' => $this->factalPaginator($novels, new NovelTransformer())
        ];
        return $this->apiSuccess($data);
    }

    public function editorRecommend($id) {//二级分类id
        $athor_ids = Author::where('wether_editor', 1)->pluck('id');
        $novel_ids = AuthorRecommend::whereIn('author_id', $athor_ids)->where('category_id', $id)->pluck('novel_id');
        $novels = Novel::whereIn('id', $novel_ids)->orderby('click_count', 'desc')->limit(10)->get();
        $data = [
            'novels' => $this->fractalItems($novels, new NovelTransformer())
        ];
        return $this->apiSuccess($data);
    }

    private function _findSecondCateGoryIds($id) {
        $first_category = NovelCategory::query()->firstCategory()->id($id)->first();
        if(empty($first_category)) {
            return [
                'errcode' => 1,
                'errmsg' => '查无此主分类',
                'data' => []
            ];
        }
        //查找子分类id集合
        $children_ids = NovelCategory::query()->pid($first_category->id)->select('id')->pluck('id');
        return [
            'errcode' => 0,
            'errmsg' => '',
            'data' => $children_ids
        ];
    }

    public function booksOfCategory($id) {
        $novel_category = NovelCategory::query()->findorfail($id);

    }
}
