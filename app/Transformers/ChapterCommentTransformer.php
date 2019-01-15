<?php

namespace App\Transformers;

use App\Model\ChapterComment;
use App\Model\User;
use League\Fractal\TransformerAbstract;

class ChapterCommentTransformer extends TransformerAbstract
{
    /*
     * 章节评论列表
     */
    public function transform(ChapterComment $item){
        return [
            'id' => $item->id,
            'user_id' => $item->user_id,
            'user_nickname' => ($this->getUserInfo($item->user_id))['nick_name'],
            'profile' => ($this->getUserInfo($item->user_id))['profile'],
            'level' => ($this->getUserInfo($item->user_id))['level'],
            'wethere_author' => ($this->getUserInfo($item->user_id))['wether_author'],
            'chapter_id' => $item->chapter_id,
            'content' => $item->content,
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
}
