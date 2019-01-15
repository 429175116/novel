<?php

namespace App\Http\Controllers\Api;

use App\Model\AddFriendNotice;
use App\Model\HotWord;
use App\Model\MonthTicket;
use App\Model\NovelCategory;
use App\Model\NovelChapter;
use App\Model\NovelMonthTicket;
use App\Model\UserHasUser;
use App\Model\UserMonthTicket;
use App\Model\UserMonthTicketRecord;
use App\Model\userSignRecord;
use App\Model\UserSubscriotion;
use App\Service\UserService;
use App\Transformers\UserSignTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Model\UserStore;
use App\Model\Novel;
use App\Model\NovelDay;
use App\Model\NovelWeek;
use App\Model\NovelMonth;
use App\Service\NovelService;

use App\Transformers\UserStoreTransformer;
use App\Transformers\UserTransformer;
use App\Transformers\AddFriendNoticeTransformer;

use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * 登录，新增或修改用户
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addOrUpdate(Request $request) {
        if(!empty($errors = $this->myValidator($request, User::$rules, User::$message))) return $errors;//验证
        $ress = $this->myCodeCheck($request); //code验证
        if(!$ress['success']) { return $ress['err']; } else { $res = $ress['data']; $input = $request->all();} //返回错误信息
        DB::beginTransaction();
        try {
            $user = $this->_setUserForAddOrUpdate(User::query()->openId($res['openid'])->first(), $res, $input);//
            $user->save();

            $where = [
                'user_id' => $user->id,
                'date' => date('Y-m-d')
            ];

            $user_sign_record = userSignRecord::where($where)->first();
            if(empty($user_sign_record)) {
                $user_sign_record = new userSignRecord();
                $user_sign_record->user_id = $user->id;
                $user_sign_record->status = 'login';//为空置位已登录
            }
            $user_sign_record->date = date('Y-m-d');
            $user_sign_record->save();


            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiError($e->getMessage());
        }

        $data = ['user' => $user];
        return $this->apiSuccess($data);
    }

    public function storeBook(Request $request, $id) {
        $novel = Novel::query()->findorfail($id);
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) { return $this->apiError($return_data['errmsg']); } else {$user = $return_data['data']; $input = $request->all(); }
        $user_store = UserStore::query()->userId($user->id)->novelId($novel->id)->first();
        if(!empty($user_store)) return $this->apiError('此书已收藏，请不要重复收藏');

        $today = date('Y-m-d'); $week = date('W'); $month = date('Y-m'); $this_year = date('Y');

        //收藏 小说收藏数加一 小说日收藏加一  周收藏加一 月收藏加一
        DB::beginTransaction();
        try {
            //添加一个鸡儿收藏
            $user_store = $this->_setUserStore(new UserStore(), $user, $novel);
            $user_store->save();
            //小说收藏数加一
            $novel->stored_count = $novel->stored_count * 1 + 1;
            $novel->save();
            //日收藏 加一
            $novel_day = NovelService::ifNovelDayEmptySetIt(NovelDay::query()->todayNovel($today, $novel->id)->first(), $novel, $today);
            $novel_day->store_count = $novel_day->store_count * 1 + 1;
            $novel_day->save();
            //周收藏 加一
            $novel_week = NovelService::ifNovelWeekEmptySetIt(NovelWeek::query()->thisWeekNovel($week, $novel->id)->first(), $week, $this_year, $novel);
            $novel_week->store_count = $novel_week->store_count * 1 + 1;
            $novel_week->save();
            //月收藏 加一
            $this_novel_month = NovelService::ifNovelMonthEmptySetIt(NovelMonth::query()->thisMonthNovel($month, $novel->id)->first(), $month, $this_year, $novel);
            $this_novel_month->store_count = $this_novel_month->store_count * 1 + 1;
            $this_novel_month->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiError($e->getMessage());
        }
        return $this->apiSuccess();
    }

    /**
     * 取消收藏
     * @param Request $request
     * @param Novel $novel
     */
    public function unStoreBook(Request $request, $id) {
        $novel = Novel::query()->findorfail($id);
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) { return $this->apiError($return_data['errmsg']); } else {$user = $return_data['data']; $input = $request->all(); }
        $user_store = UserStore::query()->userId($user->id)->novelId($novel->id)->first();
        if(empty($user_store)) return $this->apiError('此书未收藏');
        $today = date('Y-m-d'); $week = date('W'); $month = date('Y-m'); $this_year = date('Y');
        //收藏 小说收藏数减1 小说日收藏减1 周收藏减1 月收藏减1
        DB::beginTransaction();
        try {
            //收藏的去掉
            $user_store->delete();
            //小说收藏数加一
            $novel->stored_count = $novel->stored_count * 1 - 1;
            $novel->save();
            //日收藏 加一
            $novel_day = NovelService::ifNovelDayEmptySetIt(NovelDay::query()->todayNovel($today, $novel->id)->first(), $novel, $today);
            $novel_day->store_count = $novel_day->store_count * 1 - 1;
            $novel_day->save();
            //周收藏 加一
            $novel_week = NovelService::ifNovelWeekEmptySetIt(NovelWeek::query()->thisWeekNovel($week, $novel->id)->first(), $week, $this_year, $novel);
            $novel_week->store_count = $novel_week->store_count * 1 - 1;
            $novel_week->save();
            //月收藏 加一
            $this_novel_month = NovelService::ifNovelMonthEmptySetIt(NovelMonth::query()->thisMonthNovel($month, $novel->id)->first(), $month, $this_year, $novel);
            $this_novel_month->store_count = $this_novel_month->store_count * 1 - 1;
            $this_novel_month->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiError($e->getMessage());
        }
        return $this->apiSuccess();
    }

    /**
     * 购买月票
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function purchaseMonthTicket(Request $request) {
        $purchase_count = $request->input('purchase_count');//购买数量
        $purchase_time = time();
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) { return $this->apiError($return_data['errmsg']); } else {$user = $return_data['data'];}
        $month_ticket = MonthTicket::orderby('id', 'desc')->first();
        if(empty($month_ticket)) return $this->apiError('未找到该月票');

        //调取微信支付(暂时不写)
        $user_month_ticket = UserMonthTicket::where('user_id', $user->id)->first();
        if(empty($user_month_ticket)) {
            $user_month_ticket = new UserMonthTicket();
            $user_month_ticket->user_id = $user->id;
            $user_month_ticket->month_ticket_total_count = 0;
            $user_month_ticket->month_ticket_total_amount = 0;
        }
        DB::beginTransaction();
        try {
            $user_month_ticket->month_ticket_total_count = $user_month_ticket->month_ticket_total_count * 1 + $purchase_count * 1;
            $user_month_ticket->month_ticket_total_amount = $month_ticket->price * $purchase_count + $user_month_ticket->month_ticket_total_amount;
            $user_month_ticket->save();

            $user_month_ticket_record = new UserMonthTicketRecord();
            $user_month_ticket_record->user_id = $user->id;
            $user_month_ticket_record->month_ticket_count = $purchase_count * 1;
            $user_month_ticket_record->month_ticket_total_amount = $user_month_ticket_record->month_ticket_total_amount * 1 + $month_ticket->price * $purchase_count;
            $user_month_ticket_record->purchase_time = $purchase_time;
            $user_month_ticket_record->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiError($e->getMessage());
        }

        return $this->apiSuccess();
    }

    /**
     * 给小说投月票
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addMontTicket(Request $request) {//
        //获取用户
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) { return $this->apiError($return_data['errmsg']); } else {$user = $return_data['data'];}

        $novel_id = $request->input('novel_id');//小说Id
        $novel = Novel::findorfail($novel_id);
        $year = date('Y');
        $month = date('Y-m');
        $week = date('W');
        $date = date('Y-m-d');

        $month_ticket = MonthTicket::orderby('id', 'desc')->first();

        $ticket_count = $request->input('ticket_count');//投票数量
        $user_month_ticket = UserMonthTicket::where('user_id', $user->id)->first();
        if(empty($user_month_ticket) || $user_month_ticket->month_ticket_total_count < $ticket_count) return $this->apiError('月票不足');

        //投月票
        $novel_ticket = NovelMonthTicket::where('novel_id', $novel->id)->where('month', $month)->first();
        if(empty($novel_ticket)) {
            $novel_ticket = new NovelMonthTicket();
            $novel_ticket->novel_id = $novel->id;
            $novel_ticket->month = $month;
            $novel_ticket->month_tickets_count = 0;
            $novel_ticket->month_tickets_total_amount = 0;
        }
        DB::beginTransaction();
        try {
            //月票 记录一下
            $novel_ticket->month_tickets_count = $novel_ticket->month_tickets_count * 1 + $ticket_count * 1;
            $novel_ticket->month_tickets_total_amount = $novel_ticket->month_tickets_total_amount * 1 + $ticket_count * $month_ticket->price;
            $novel_ticket->save();

            //小说日统计 加月票
            $novel_day = NovelDay::where('date', $date)->where('novel_id', $novel->id)->first();
            if(empty($novel_day)) {
                $novel_day = new NovelDay();
                $novel_day->date = $date;
                $novel_day->novel_id = $novel->id;
                $novel_day->category_id = $novel->novel_categories_id;
                $novel_day->month_ticket_count = 0;
                $novel_day->month_ticket_toal_amount = 0;
            }
            $novel_day->month_ticket_count = $novel_day->month_ticket_count * 1 + $ticket_count * 1;
            $novel_day->month_ticket_toal_amount = $novel_day->month_ticket_toal_amount * 1 + $ticket_count * $month_ticket->price;
            $novel_day->save();

            //小说周 记录
            $novel_week =  NovelWeek::where('week', $week)->where('novel_id', $novel->id)->where('year', $year)->first();
            if(empty($novel_week)) {
                $novel_week = new NovelWeek();
                $novel_week->novel_id = $novel->id;
                $novel_week->category_id = $novel->novel_categories_id;
                $novel_week->week = $week;
                $novel_week->year = $year;
                $novel_week->month_ticket_count = 0;
                $novel_week->month_ticket_toal_amount = 0;
            }
            $novel_week->month_ticket_count = $novel_week->month_ticket_count * 1 + $ticket_count * 1;
            $novel_week->month_ticket_toal_amount = $novel_week->month_ticket_toal_amount * 1 + $ticket_count * $month_ticket->price;
            $novel_week->save();

            //小说月 记录
            $novel_month = NovelMonth::where('month', $month)->where('novel_id', $novel->id)->first();
            if(empty($novel_month)) {
                $novel_month = new NovelMonth();
                $novel_month->novel_id = $novel->id;
                $novel_month->category_id = $novel->novel_categories_id;
                $novel_month->month = $month;
                $novel_month->year = $year;
                $novel_month->month_ticket_count = 0;
                $novel_month->month_ticket_toal_amount = 0;
            }
            $novel_month->month_ticket_count = $novel_month->month_ticket_count * 1 + $ticket_count * 1;
            $novel_month->month_ticket_toal_amount = $novel_month->month_ticket_toal_amount * 1 + $ticket_count * $month_ticket->price;
            $novel_month->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiError($e->getMessage());
        }
        return $this->apiSuccess();
    }

    /**
     * 我的收藏
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myStore(Request $request) {
        //获取用户
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) { return $this->apiError($return_data['errmsg']); } else {$user = $return_data['data'];}
        $user_stores = UserStore::query()->userId($user->id)->get();

        $data = [
            'user_stores' => $this->fractalItems($user_stores, new UserStoreTransformer())
        ];
        return $this->apiSuccess($data);
    }

    public function searchHistory() {

    }

    //添加好友
    public function addFriend(Request $request) {
        //获取用户信息
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) return $this->apiError($return_data['errmsg']);
        $user = $return_data['data'];

        $friend_id = $request->input('friend_id');
        $message = $request->input('message');

        $where = [
            'from' => $user->id,
            'to' => $friend_id,
            'status' => 'un_reaad'
        ];

        $add_friend_notice = AddFriendNotice::where($where)->first();
        if(!empty($add_friend_notice)) return $this->apiError('正在请求对方通过，请不要重复发送');

        $add_friend_notice = new AddFriendNotice();
        $add_friend_notice->from = $user->id;
        $add_friend_notice->to = $friend_id;
        $add_friend_notice->status = 'un_reaad';
        $add_friend_notice->message = $message;
        $add_friend_notice->save();
        return $this->apiSuccess();
    }

    //对好友请求做同意或不同意
    public function agreeOrDisagree(Request $request) {

        //获取用户信息 身份验证
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) return $this->apiError($return_data['errmsg']);
        $user = $return_data['data'];

        $id = $request->input('id');//notice_id
        $status = $request->input('status');//'un_reaad' => '未读','agree' => '同意','disagree' => '拒绝'
        if(empty($status) || ($status != 'agree' && $status != 'disagree')) return $this->apiError('请选择同意或不同意');
        $add_friend_notice = AddFriendNotice::findorfail($id);
        if($status == 'disagree') {
            $add_friend_notice->status = 'disagree';
            $add_friend_notice->save();
            return $this->apiSuccess();
        }

        $where_positive = [
            'from_id' => $add_friend_notice->from,
            'to_id' => $add_friend_notice->to,
        ];//正向

        $where_negetive = [
            'from_id' => $add_friend_notice->to,
            'to_id' => $add_friend_notice->from,
        ];

        $positive_ralation = UserHasUser::where($where_positive)->first();
        $negetive_ralation = UserHasUser::where($where_negetive)->first();

        DB::beginTransaction();
        try {
            if(empty($positive_ralation)) {
                $positive_ralation = new UserHasUser();
                $positive_ralation->from_id = $add_friend_notice->from;
                $positive_ralation->to_id = $add_friend_notice->to;
                $positive_ralation->save();
            }
            if(empty($negetive_ralation)) {
                $negetive_ralation = new UserHasUser();
                $negetive_ralation->from_id = $add_friend_notice->to;
                $negetive_ralation->to_id = $add_friend_notice->from;
                $negetive_ralation->save();
            }
            //通知改成同意
            $add_friend_notice->status = 'agree';
            $add_friend_notice->save();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiError($e->getMessage());
        }
        return $this->apiSuccess();
    }

    //我的好友
    public function myfriend(Request $request) {
        //获取用户信息
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) return $this->apiError($return_data['errmsg']);
        $user = $return_data['data'];

        $nick_name = $request->input('nick_name');

        $friends = $user->friend()->nickName($nick_name)->paginate(10);
        $data['data'] = [
            'friends' => $this->factalPaginator($friends, new UserTransformer())
        ];
        return $this->apiSuccess($data);
    }

    /**
     * 书友在读
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myfriendRead(Request $request) {
        //获取用户信息
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) return $this->apiError($return_data['errmsg']);
        $user = $return_data['data'];
        $friends = $user->friend()->limit(10)->get()->toArray();
        $friends_ids = [];
        foreach ($friends as $fr) {
            array_push($friends_ids, $fr['id']);
        }
        $friends_novels = UserStore::query()->whereIn('user_id', $friends_ids)->limit(10)->get();
        $data = [
            'friends_novels' => $this->fractalItems($friends_novels, new UserStoreTransformer())
        ];
        return $this->apiSuccess($data);
    }


    //删除好友
    public function deleteFriend(Request $request, $id) {

        //获取用户信息 身份验证
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) return $this->apiError($return_data['errmsg']);
        $user = $return_data['data'];

        $where_positive = [
            'from_id' => $user->id,
            'to_id' => $id,
        ];//正向

        $where_negetive = [
            'from_id' => $id,
            'to_id' => $user->id,
        ];
        DB::beginTransaction();
        try {
            $positive_ralation = UserHasUser::where($where_positive)->first();
            $negetive_ralation = UserHasUser::where($where_negetive)->first();

            if(!empty($positive_ralation)) $positive_ralation->delete();
            if(!empty($negetive_ralation)) $negetive_ralation->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiError($e->getMessage());
        }
        return $this->apiSuccess();
    }

    /**
     * 好友请求信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myFriendRequest(Request $request) {
        //获取用户信息
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) return $this->apiError($return_data['errmsg']);
        $user = $return_data['data'];
        $where = [
            'to' => $user->id,
            'status' => 'un_reaad'
        ];
        $add_friend_notices = AddFriendNotice::where($where)->paginate(10);
        $data['data'] = [
            'add_friend_notices' => $this->factalPaginator($add_friend_notices, new AddFriendNoticeTransformer())
        ];
        return $this->apiSuccess($data);
    }

    //我的好友请求数量
    public function myFriendRequstCount(Request $request) {
        //获取用户信息
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) return $this->apiError($return_data['errmsg']);
        $user = $return_data['data'];
        //只选择未读的请求

        $where = [
            'to' => $user->id,
            'status' => 'un_reaad'
        ];

        $count = AddFriendNotice::where($where)->count();
        $data['data'] = [
            'friend_request_count' => $count
        ];

        return $this->apiSuccess($data);
    }

    /**
     * 成为会员
     */
    public function tobeVip() {

    }

    /**
     * 添加搜索热词
     * @param Request $request
     */
    public function addSearchHotWord(Request $request) {

        $word = $request->input('word');

        $hot_word = HotWord::query()->where('words', $word)->first();
        if(empty($hot_word)) {
            $hot_word = new HotWord();
            $hot_word->words = $word;
            $hot_word->word_count = 0;
        }
        $hot_word->word_count = $hot_word->word_count * 1 + 1;
        $hot_word->save();
        return $this->apiSuccess();
    }


    private function _setUserStore($user_store, $user, $novel) {
        $user_store->user_id = $user->id;
        $user_store->novel_id = $novel->id;
        return $user_store;
    }

    private function _setUserForAddOrUpdate($user, $res, $input) {
        if(empty($user)){
            $user = new User();
            $user->open_id = $res['openid'];
        }
        $user->profile = $input['profile'];
        $user->nick_name = $input['nick_name'];
        return $user;
    }

    public function userSignInformation(Request $request) {
        //获取用户信息
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) return $this->apiError($return_data['errmsg']);
        $user = $return_data['data'];

        $last_signin = userSignRecord::where('user_id', $user->id)->orderby('id', 'desc')->first();

        $data['data'] = [
            'continuous_day' => $this->fractalItems($last_signin, new UserSignTransformer())
        ];
        return $this->apiSuccess($data);
    }

    public function subscriptionNovel(Request $request) {
        //获取用户信息
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) return $this->apiError($return_data['errmsg']);
        $user = $return_data['data'];

        $novel_chapter_id = $request->input('novel_chapter_id');
        $novel_id = $request->input('novel_id');

        $novel_chapter = NovelChapter::findorfail($novel_chapter_id);
        $novel = Novel::findorfail($novel_id);

        $user_subscription = UserSubscriotion::where('user_id', $user->id)->where('novel_chapter_id', $novel_chapter_id)->first();
        if(!empty($user_subscription)) return $this->apiError('已订阅此章节');
        $user_subscription = new UserSubscriotion();
        $user_subscription->user_id = $user->id;
        $user_subscription->novel_chapter_id = $novel_chapter_id;
        $user_subscription->novel_id = $novel_id;
        $user_subscription->category_id = $novel->novel_categories_id;
        $user_subscription->save();

        return $this->apiSuccess();
    }

    public function userSubscription(Request $request) {
        //获取用户信息
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) return $this->apiError($return_data['errmsg']);
        $user = $return_data['data'];

        $user_subscriptions = UserSubscriotion::where('user_id', $user->id)->get();

        $data = [
            'user_subscriptions' => $user_subscriptions
        ];
        return $this->apiSuccess($data);
    }

}
