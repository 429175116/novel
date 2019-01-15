<?php

namespace App\Http\Controllers\Api;

use App\Transformers\NovelCommentTransformer;
use App\Model\Novel;
use App\Model\NovelComment;
use App\Model\NovelLikes;
use App\Model\User;
use App\Transformers\NovelCategoryTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class NovelCommentController extends Controller
{
    //用户给小说评论
    public function firstNovelComment(Request $request)
    {
        $userId = $request->input('user_id');
        $novelId = $request->input('novel_id');
        $content = $request->input('content');

        $user_id = User::query()->findorfail($userId); unset($user_id);
        $novel_id = Novel::query()->findorfail($novelId); unset($novel_id);
        if(empty($content)) {return $this->apiError("评论不能为空");}

        $novelComment = new NovelComment();
        $novelComment->user_id = $userId;
        $novelComment->novel_id = $novelId;
        $novelComment->content = $content;
        $novelComment->save();

        return $this->apiSuccess();
    }

    //用户给小说评论进行评论
    public function secondNovelComment(Request $request)
    {
        $userId = $request->input('user_id');
        $novelId = $request->input('novel_id');
        $content = $request->input('content');
        $parentId = $request->input('parent_id');

        $user_id = User::query()->findorfail($userId); unset($user_id);
        $novel_id = Novel::query()->findorfail($novelId); unset($novel_id);
        $parent_id = NovelComment::query()->findorfail($parentId); unset($parent_id);
        if(empty($content)) {return $this->apiError("评论不能为空");}

        $novelComment = new NovelComment();
        $novelComment->user_id = $userId;
        $novelComment->novel_id = $novelId;
        $novelComment->content = $content;
        $novelComment->parent_id = $parentId;
        $novelComment->save();

        return $this->apiSuccess();
    }

    //用户给小说评论点赞
    public function novelLike(Request $request)
    {
        $novelCommentId = $request->input('novelComment_id');
        $userId = $request->input('user_id');
        $novelId = $request->input('novel_id');

        $novelComment_id = NovelComment::query()->findorfail($novelCommentId); unset($novelComment_id);
        $user_id = NovelComment::query()->findorfail($userId); unset($user_id);
        $novel_id = NovelComment::query()->findorfail($novelId); unset($novel_id);

        $findLike = NovelLikes::query()->where('user_id',$userId)->where('comment_id',$novelCommentId)->first();

        DB::beginTransaction();
        try{
            if(!$findLike)
            {
                $novelLike = new NovelLikes();
                $novelLike->user_id = $userId;
                $novelLike->novel_id = $novelId;
                $novelLike->comment_id = $novelCommentId;
                $novelLike->save();

                $novelComment = NovelComment::query()->findOrFail($novelCommentId);
                $novelComment->likes_number = $novelComment->likes_number * 1 + 1;
                $novelComment->save();
            }else{
                $findLike->delete();

                $novelComment = NovelComment::query()->findOrFail($novelCommentId);
                $novelComment->likes_number = $novelComment->likes_number * 1 - 1;
                $novelComment->save();
            }

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return $this->apiError($e);
        }

        return $this->apiSuccess();
    }


    /**
     * 小说一级评论获取
     * @param Novel $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function NovelCommentList($id)
    {
        $novel_comment_list = NovelComment::query()->where('novel_id',$id)->where('parent_id',0)->paginate(10);
        $data = [
          'first_novel_comment' => $this->factalPaginator($novel_comment_list,new NovelCommentTransformer())
        ];
        return $this->apiSuccess($data);
    }


    /**
     * 小说二级评论获取
     * @param NovelComment $parent_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function subNovelCommentList($id) {
        $parent = NovelComment::query()->findorfail($id);

        $sub_comment_list = NovelComment::query()->where('parent_id', $parent->id)->paginate(10);
        $data = [
            'parent' => $this->fractalItem($parent, new NovelCommentTransformer()),
            'sub_novel_comment_list' => $this->factalPaginator($sub_comment_list, new NovelCommentTransformer())
        ];
        return $this->apiSuccess($data);
    }





}
