<?php

namespace App\Http\Controllers\Backend;

use App\Model\NovelChapterContent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Novel;
use App\Model\NovelChapter;
//use App\Model\NovelChapterContent;
use App\Transformers\NovelTransformer;
use Illuminate\Support\Facades\DB;

class NovelController extends Controller
{
    /**
     * 详情
     * @param Novel $novel
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(Novel $novel) {
        $data = [
            'novel' => $this->fractalItem($novel, NovelTransformer::forDetail())
        ];
        return $this->apiSuccess($data);
    }

    public function chapterContent(NovelChapter $novelChapter) {

    }

    /**
     * 添加书
     * @param Request $request
     */
    public function createNovelForAll(Request $request) {
//        $author = $request->user('api');
        if(!empty($errors = $this->myValidator($request, Novel::$rules, Novel::$message))) return $errors;//验证
        $input = $request->all();
        $novel = $this->_setNovelForCreateNovel(new Novel(), $input);
        $novel->save();
        return $this->apiSuccess();
    }

    /**
     * 添加章节
     * @param Request $request
     */
    public function addChapterForAll(Request $request) {//日 周 月 日数统计要改变
        $input = $request->all();//取值
        if(!empty($errors = $this->myValidator($request, NovelChapter::$rules, NovelChapter::$message))) return $errors;//验证
        $novel = Novel::findorfail($input['novel_id']);
        $today = date('Y-m-d'); $week = date('W'); $month = date('Y-m'); $this_year = date('Y');
        DB::beginTransaction();
        try {
            // 添加小说章节
            $novel_chapter = $this->_setNovelChapter(new NovelChapter(), $input);
            $novel_chapter->novel_id = $novel->id;
            $novel_chapter->save();
            //添加小说内容
            $novel_chapter_content = $this->_setNovelContent(new NovelChapterContent(), $novel_chapter, $input);
            $novel_chapter_content->save();
            //小说字数增加
            $novel->words_count = $novel->words_count * 1 + mb_strlen($input['content'], 'utf-8') * 1;
            $novel->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiError($e->getMessage());
        }
        return $this->apiSuccess();
    }

    /**
     * 修改章节
     * @param Request $request
     * @param NovelChapter $novel_chapter
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function editChapterForAll(Request $request, NovelChapter $novel_chapter) {// 日 周 月 数字统计改变
        $input = $request->all();//取值
        $novel = Novel::findorfail($novel_chapter->novel_id);
        if(!empty($errors = $this->myValidator($request, NovelChapter::$rules, NovelChapter::$message))) return $errors;//验证
        $novel_chapter_content = NovelChapterContent::query()->where('novel_chapter_id', $novel_chapter->id)->first();
        DB::beginTransaction();
        try {
            $orginal_words_count = $novel_chapter->words_count;
            // 添加小说章节
            $novel_chapter = $this->_setNovelChapter($novel_chapter, $input);
            $novel_chapter->save();
            //修改小说内容
            if(empty($novel_chapter_content)) {
                $novel_chapter_content = new NovelChapterContent();
            }
            $novel_chapter_content = $this->_setNovelContent($novel_chapter_content, $novel_chapter, $input);
            $novel_chapter_content->save();
            //小说字数改变
            $novel->words_count = $novel->words_count * 1 + ($novel_chapter->words_count *1 - $orginal_words_count *1);
            $novel->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiError($e->getMessage());
        }
        return $this->apiSuccess();
    }

    private function _setNovelChapter($novel_chapter, $input) {
        $novel_chapter->title = $input['title'];
        $novel_chapter->words_count = mb_strlen($input['content'], 'utf-8');
        return $novel_chapter;
    }

    private function _setNovelContent($novel_chapter_content, $novel_chapter, $input) {
        $novel_chapter_content->novel_chapter_id = $novel_chapter->id;
        $novel_chapter_content->content = $input['content'];
        return $novel_chapter_content;
    }


    private function _setNovelForCreateNovel($novel, $input) {
        $novel->author_id = 1;
        $novel->name = $input['name'];
        $novel->intro =  $input['intro'];
        $novel->img = $input['img'];
        $novel->novel_categories_id = $input['novel_categories_id'];
        return $novel;
    }
}
