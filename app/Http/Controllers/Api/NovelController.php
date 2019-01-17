<?php

namespace App\Http\Controllers\Api;

use App\Model\Author;
use App\Model\AuthorRecommend;
use App\Model\HotWord;
use App\Model\NovelChapter;
use App\Model\NovelChapterContent;
use App\Model\UserStore;
use App\Service\UserService;
use App\Transformers\NovelChapterTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Novel;
use App\Transformers\NovelTransformer;
use App\Transformers\UserStoreTransformer;
use App\Transformers\AuthorTransformer;
use App\Transformers\HotWordTransformer;
use App\Model\NovelCategory;
use Illuminate\Support\Facades\DB;


class NovelController extends Controller
{

    public function searchNovel(Request $request) {
        $novel_name = $request->input('novel_name');

        $novels = Novel::name($novel_name)->paginate(10);

        $data = [
            'novels' => $this->factalPaginator($novels, new NovelTransformer())
        ];
        return $this->apiSuccess($data);
    }

    public function rolling() {
        $novels = Novel::orderby('read_count', 'desc')->limit(3)->get();
        $data = [
            'rolling_img' => $this->fractalItems($novels, NovelTransformer::forRolling())
        ];
        return $this->apiSuccess($data);
    }


    public function hotWords() {
        $hot_worlds = HotWord::orderby('word_count', 'desc')->limit(10)->get();
        $data = [
            'hot_words' => $this->fractalItems($hot_worlds, new HotWordTransformer())
        ];
        return $this->apiSuccess($data);
    }

    /**
     * 热门书籍
     * @return \Illuminate\Http\JsonResponse
     */
    public function hotNovel() {
        $first_category = NovelCategory::pid(0)->get();
        $data = [];
        foreach ($first_category as $first) {
            $second_categories_ids = NovelCategory::pid($first->id)->select('id')->pluck('id');
            $hot_novels = $this->fractalItems(Novel::whereIn('novel_categories_id', $second_categories_ids)->orderby('click_count', 'desc')->limit(5)->get(), new NovelTransformer());
            $tmp = [
                'parent' =>  $first,
                'hot_novels' => $hot_novels
            ];
            array_push($data, $tmp);
        }
        return $this->apiSuccess($data);
    }


    //书籍相关作家的其他作品
    public function authorOtherNovel($id) {//小说Id
        $novel = Novel::findorfail($id);
        $author = Author::findorfail($novel->author_id);

        $other_novel_count = Novel::where('author_id', $author->id)->where('id', '<>', $id)->count();
        $other_novels = Novel::where('author_id', $author->id)->where('id', '<>', $id)->get();

        $data = [
            'count' => $other_novel_count,
            'other_novels' => $this->fractalItems($other_novels, new NovelTransformer())
        ];
        return $this->apiSuccess($data);
    }

    /**
     * 免费新书
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function freeNewNovel(Request $request) {
        $novels = Novel::query()->where('wether_free', 1)->orderby('id', 'desc')->limit(20)->get();
        $data = [
            'novels' => $this->fractalItems($novels, new NovelTransformer())
        ];
        return $this->apiSuccess($data);
    }


    /**
     * 推荐书籍
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recommendNovel(Request $request) {
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) { return $this->apiError($return_data['errmsg']); } else {$user = $return_data['data'];}

        $novel_ids = UserStore::query()->where('user_id', $user->id)->select('novel_id')->pluck('novel_id');
        $nvoel_category_ids = Novel::query()->whereIn('id', $novel_ids)->select('novel_categories_id')->pluck('novel_categories_id');
        $recommend_novels = Novel::query()->where('novel_categories_id', $nvoel_category_ids)->whereNotIn('id', $novel_ids)->orderby('click_count', 'desc')->limit(10)->get();
        $data = [
            'recommend_novels' => $this->fractalItems($recommend_novels, new NovelTransformer())
        ];
        return $this->apiSuccess($data);
    }

    //喜欢这本书的人也喜欢
    public function userLikeThisBookLikeOther(Request $request, $id) {//小说Id
        //获取用户
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) { return $this->apiError($return_data['errmsg']); } else {$user = $return_data['data'];}

        $novel = Novel::findorfail($id);
        $other_users = UserStore::where('novel_id', $novel->id)->where('user_id', '<>', $user->id)->select('user_id')->pluck('user_id');

        $other_novels = UserStore::where('novel_id', '<>', $novel->id)->whereIn('user_id', $other_users)->limit(10)->get();
        $data = [
            'other_novels' => $this->fractalItems($other_novels, new UserStoreTransformer())
        ];
        return $this->apiSuccess($data);
    }

    /**
     * 详情
     * @param Novel $novel
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail($id) {
        $novel = Novel::findorfail($id);
        $author = Author::findorfail($novel->author_id);
        $data = [
            'novel' => $this->fractalItem($novel, NovelTransformer::forDetail()),
            'author' => $this->fractalItem($author, new AuthorTransformer())
        ];
        return $this->apiSuccess($data);
    }


    public function chapterDetail(Request $request) {
        $novel_id = $request->input('novel_id');
        $chapter_id = $request->input('chapter_id');
        $novel_chapter = NovelChapter::where('novel_id', $novel_id)->where('id', '>=', $chapter_id)->paginate(1);
        $data = [
            'novel_chapter' => $this->factalPaginator($novel_chapter, new NovelChapterTransformer())
        ];
        return $this->apiSuccess($data);
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

    public function addNovelAndChapter(Request $request) {

        $book_id = $request->input('book_id');
        $novel_id = $request->input('novel_id');
        $novel = Novel::findorfail($novel_id);

        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', 'http://api.zhuishushenqi.com/btoc?view=summary&book='.$book_id);
        if($res->getStatusCode() == 200) {
            $book = json_decode($res->getBody(), true);
            $source_id = $book[0]['_id'];
            //获取章节 source 的那种
            $res2 = $client->request('GET', 'http://api.zhuishushenqi.com/atoc/'.$source_id.'?view=chapters');
            if($res2->getStatusCode() == 200) {
                $chapters = json_decode($res2->getBody(), true);
                $chapters_new = array_slice($chapters['chapters'],101,100);
                DB::beginTransaction();
                try {
                    foreach ($chapters_new as $chapter) {
                        //存章节
                        $novel_chapter = new NovelChapter();
                        $novel_chapter->title = $chapter['title'];
                        $novel_chapter->novel_id = $novel->id;
                        $novel_chapter->save();
                        //添加小说内容

                        $res_tmp = $client->request('GET', 'http://chapterup.zhuishushenqi.com/chapter/'.$chapter['link']);
                        if($res_tmp->getStatusCode() == 200) {
                            $chapter_content = json_decode($res_tmp->getBody(), true);
                            //存章节内容
                            $novel_chapter->words_count = mb_strlen($chapter_content['chapter']['cpContent'], 'utf-8');
                            $novel_chapter->save();

                            $novel_chapter_content = new NovelChapterContent();
                            $novel_chapter_content->novel_chapter_id = $novel_chapter->id;
                            $novel_chapter_content->content = $chapter_content['chapter']['cpContent'];
                            $novel_chapter_content->save();

                            //小说字数增加
                            $novel->words_count = $novel->words_count * 1 + mb_strlen($chapter_content['chapter']['cpContent'], 'utf-8') * 1;
                            $novel->save();
                        }

                    }
                    DB::commit();

                } catch (\Exception $e) {
                    DB::rollback();
                    return $this->apiError($e->getMessage());
                }

            }

            return $this->apiSuccess();
        }else{
            return $this->apiError('查询出错，请查询网络是否有问题');
        }
    }


}
