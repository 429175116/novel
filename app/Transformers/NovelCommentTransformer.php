<?php

namespace App\Transformers;

use App\Model\NovelComment;
use App\Model\User;
use League\Fractal\TransformerAbstract;

class NovelCommentTransformer extends TransformerAbstract
{
    /*
     * 小说评论列表
     */
    public function transform(NovelComment $item){
        return [
            'id' => $item->id,
            'user_id' => $item->user_id,
            'user_nickname' => ($this->getUserInfo($item->user_id))['nick_name'],
            'profile' => ($this->getUserInfo($item->user_id))['profile'],
            'level' => ($this->getUserInfo($item->user_id))['level'],
            'wethere_author' => ($this->getUserInfo($item->user_id))['wether_author'],
            'novel_id' => $item->novel_id,
            'content' => $item->content,
            'subCommentCount' => ($this->getSubCommentCount($item->id)),
            'likes_number' => $item->likes_number,
            'created_at' =>$item->created_at->toDateTimeString(),
            'updated_at' =>$item->updated_at->toDateTimeString()
        ];
    }


    /*
     * 获取用户信息
     * @param User $user_id
     */
    public function getUserInfo($user_id)
    {
        $user = User::query()->findorfail($user_id);
        $nick_name = $user->nick_name;
        $profile = $user->profile;
        $level = $user->level;
        $wether_author = $user->wether_author;
        $data = [
            'nick_name' => $nick_name,
            'profile' => $profile,
            'level' => $level,
            'wether_author' => $wether_author
        ];

        return $data;
    }

    /*
     * 获取子评论数量
     * @param NovelComment id
     */
    public function getSubCommentCount($id){
        return NovelComment::where('parent_id',$id)->count();
    }
}
