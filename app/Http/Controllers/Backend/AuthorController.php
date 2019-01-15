<?php

namespace App\Http\Controllers\Backend;

use App\Model\Author;
use App\Model\AuthorRecommend;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Novel;
use App\Model\NovelDay;
use App\Model\NovelWeek;
use App\Model\NovelMonth;
use App\Model\NovelTag;

use App\Model\NovelChapter;
use App\Model\NovelChapterContent;

use App\Service\NovelService;

use App\Transformers\NovelTransformer;
use Illuminate\Support\Facades\DB;

class AuthorController extends Controller
{
    /**
     * 添加书
     * @param Request $request
     */
    public function createNovel(Request $request) {
        $author = $request->user('api');
        if(!empty($errors = $this->myValidator($request, Novel::$rules, Novel::$message))) return $errors;//验证
        $input = $request->all();
        $novel = $this->_setNovelForCreateNovel(new Novel(), $author, $input);
        $novel->save();
        return $this->apiSuccess();
    }

    private function _setNovelForCreateNovel($novel, $author, $input) {
        $novel->author_id = $author->id;
        $novel->name = $input['name'];
        $novel->intro =  $input['intro'];
        $novel->img = $input['img'];
        $novel->novel_categories_id = $input['novel_categories_id'];
        return $novel;
    }

    /**
     * 我的小说列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myNovel(Request $request) {
        $author = $request->user('api');
        $my_novels = Novel::query()->where('author_id', $author->id)->get();
        $data = [
            'my_novels' => $this->fractalItems($my_novels, new NovelTransformer())
        ];
        return $this->apiSuccess($data);
    }

    /**
     * 添加章节
     * @param Request $request
     */
    public function addChapter(Request $request) {//日 周 月 日数统计要改变
        $author = $request->user('api'); unset($author); $input = $request->all();//取值
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
    public function editChapter(Request $request, NovelChapter $novel_chapter) {// 日 周 月 数字统计改变
        $author = $request->user('api'); unset($author); $input = $request->all();//取值
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

    public function addTag(Request $request, $novel_id) {
        $author = $request->user('api'); unset($author);

        $novel = Novel::query()->findorfail($novel_id);
        $tag_name = $request->input('tag_name');

        $novel_tag = NovelTag::query()->where('name', $tag_name)->where('novel_id', $novel->id)->first();
        if(!empty($novel_tag)) return $this->apiError('此标签已存在');
        $novel_tag = new NovelTag();
        $novel_tag->novel_id = $novel->id;
        $novel_tag->name = $tag_name;
        $novel_tag->save();

        return $this->apiSuccess();
    }

    public function deletetag(Request $request, $novel_id) {
        $author = $request->user('api'); unset($author);
        $novel = Novel::query()->findorfail($novel_id);
        $tag_name = $request->input('tag_name');

        $novel_tag = NovelTag::query()->where('name', $tag_name)->where('novel_id', $novel->id)->first();
        $novel_tag->delete();
        return $this->apiSuccess();
    }

    //主编加入书籍至推荐
    public function addEditorRecommend(Request $request, $novel_id) {//这个人是编辑
        $author = $request->user('api');
        if(!$author->wether_editor) return $this->apiError('不是编辑，无法添加编辑推荐');

        $novel = Novel::findorfail($novel_id);
        $author_recommend = AuthorRecommend::where('author_id', $author->id)->where('novel_id', $novel_id)->first();
        if(!empty($author_recommend)) return $this->apiError('已添加进入收藏');
        $author_recommend = new AuthorRecommend();
        $author_recommend->author_id = $author->id;
        $author_recommend->novel_id = $novel_id;
        $author_recommend->category_id = $novel->novel_categories_id;
        $author_recommend->save();

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

}
