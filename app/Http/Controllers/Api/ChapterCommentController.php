<?php

namespace App\Http\Controllers\Api;

use App\Transformers\ChapterCommentTransformer;
use App\Model\ChapterComment;
use App\Model\ChapterLike;
use App\Model\NovelChapter;
use App\Model\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ChapterCommentController extends Controller
{
    //用户评论章节
    public function firstChapterComment(Request $request)
    {
        $userId = $request->input('user_id');
        $chapterId = $request->input('chapter_id');
        $content = $request->input('content');

        $user_id = User::query()->findorfail($userId); unset($user_id);
        $chapter_id = NovelChapter::query()->findorfail($chapterId); unset($chapter_id);
        if(empty($content)) {return $this->apiError("评论不能为空");}

        $chapterComment = new ChapterComment();
        $chapterComment->user_id = $userId;
        $chapterComment->chapter_id = $chapterId;
        $chapterComment->content = $content;

        $chapterComment->save();

        return $this->apiSuccess();
    }

    //用户评论已有的章节评论
    public function secondChapterComment(Request $request)
    {
        $userId = $request->input('user_id');
        $chapterId = $request->input('chapter_id');
        $content = $request->input('content');
        $parentId = $request->input('parent_id');

        $user_id = User::query()->findorfail($userId); unset($user_id);
        $chapter_id = NovelChapter::query()->findorfail($chapterId); unset($chapter_id);
        $parent_id = ChapterComment::query()->findorfail($parentId); unset($parent_id);
        if(empty($content)) {return $this->apiError("评论不能为空");}

        $chapterComment = new ChapterComment();
        $chapterComment->user_id = $userId;
        $chapterComment->chapter_id = $chapterId;
        $chapterComment->content = $content;
        $chapterComment->parent_id = $parentId;

        $chapterComment->save();

        return $this->apiSuccess();
    }

    //用户给评论点赞
    public function chapterLike(Request $request)
    {
        $userId = $request->input('user_id');
        $chapterCommentId = $request->input('chapterComment_id');
        $chapterId = $request->input('chapter_id');

        $user_id = User::query()->findorfail($userId); unset($user_id);
        $chapterComment_id = ChapterComment::query()->findorfail($chapterCommentId); unset($chapterComment_id);
        $chapter_id = NovelChapter::query()->findorfail($chapterId); unset($chapter_id);

        $findLike = ChapterLike::query()->where('user_id',$userId)->where('comment_id',$chapterCommentId)->first();


        DB::beginTransaction();
        try {
            if(!$findLike)
            {
                $like = new ChapterLike();
                $like->comment_id = $chapterCommentId;
                $like->user_id = $userId;
                $like->chapter_id = $chapterId;

                $like->save();

                $chapterComment = ChapterComment::query()->find($chapterCommentId);
                $chapterComment->likes_number = $chapterComment->likes_number * 1 + 1;
                $chapterComment->save();
            }else{
                $findLike->delete();

                $chapterComment = ChapterComment::query()->find($chapterCommentId);
                $chapterComment->likes_number = $chapterComment->likes_number * 1 - 1;
                $chapterComment->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiError($e->getMessage());
        }

        return $this->apiSuccess();
    }


    /**
     * 章节一级分类获取
     * @param NovelChapter $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function chapterCommentList($id)
    {
        $chapter_comment_list = ChapterComment::query()->where('chapter_id',$id)->where('parent_id',0)->paginate(20);
        $data = [
            'first_chapter_comment' => $this->factalPaginator($chapter_comment_list,new ChapterCommentTransformer())
        ];
        return $this->apiSuccess($data);
    }


    /**
     * 章节二级分类获取
     * @param ChapterComment $parent_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function subChapterCommentList($id) {
        $parent = ChapterComment::query()->findorfail($id);

        $sub_comment_list = ChapterComment::query()->where('parent_id', $parent->id)->paginate(20);
        $data = [
            'parent' => $this->fractalItem($parent, new ChapterCommentTransformer()),
            'sub_chapter_comment_list' => $this->factalPaginator($sub_comment_list, new ChapterCommentTransformer())
        ];
        return $this->apiSuccess($data);
    }

}
