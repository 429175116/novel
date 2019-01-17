<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
//    'middleware' => [
//        'serializer:array',
//        'check_code'
//    ]
], function($api) {
    /**
     * 用户相关操作
     */
    $api->post('/addOrUpdateUser', 'UserController@addOrUpdate');
    $api->put('/storeBook/{id}', 'UserController@storeBook');
    $api->put('/unStoreBook/{id}', 'UserController@unStoreBook');
    $api->post('/purchaseMonthTicket', 'UserController@purchaseMonthTicket');
    $api->post('/addMontTicket', 'UserController@addMontTicket');
    $api->get('/myStore', 'UserController@myStore');

    $api->post('/addFriend', 'UserController@addFriend');
    $api->post('/agreeOrDisagree', 'UserController@agreeOrDisagree');
    $api->get('/myfriend', 'UserController@myfriend');
    $api->delete('/deleteFriend/{id}', 'UserController@deleteFriend');
    $api->get('/myFriendRequest', 'UserController@myFriendRequest');
    $api->get('/myfriendRead', 'UserController@myfriendRead');
    /**
     * 作家相关
     */
    $api->post('/applicateToBeAnAnthor', 'AuthorController@applicateToBeAnAnthor');

    /**
     * 小说相关
     */
    $api->get('/hotNovel', 'NovelController@hotNovel');
    $api->get('/authorOtherNovel/{id}', 'NovelController@authorOtherNovel');
    $api->get('/userLikeThisBookLikeOther/{id}', 'NovelController@userLikeThisBookLikeOther');
    //$api->get('/freeNewNovel', 'NovelController@freeNewNovel');
    $api->get('/freeNewNovel', function(){echo 123;});
    $api->get('/recommendNovel', 'NovelController@recommendNovel');
    $api->get('/novelDetail/{id}', 'NovelController@detail');
    $api->get('/chapterDetail', 'NovelController@chapterDetail');
    $api->get('/searchNovel', 'NovelController@searchNovel');
    $api->get('/hotWords', 'NovelController@hotWords');
    $api->get('/rolling', 'NovelController@rolling');




    //添加小说接口
    $api->get('/addNovelAndChapter','NovelController@addNovelAndChapter');


    /**
     * 小说分类
     */
    //一级分类
    $api->get('/firstCategory', 'NovelCategoryController@firstCategory');
    $api->get('/secondCategory/{id}', 'NovelCategoryController@secondCategory');
    $api->get('/newBooksOfFirstCategory/{id}', 'NovelCategoryController@newBooksOfFirstCategory');
    $api->get('/popularUncompelete/{id}', 'NovelCategoryController@popularUncompelete');
    $api->get('/popularCateGory/{id}', 'NovelCategoryController@popularCateGory');
    $api->get('/clickWeekBang/{id}', 'NovelCategoryController@clickWeekBang');
    $api->get('/twentyFourHourHotSale/{id}', 'NovelCategoryController@twentyFourHourHotSale');
    $api->get('/compeleteBoutique/{id}', 'NovelCategoryController@compeleteBoutique');
    //二级分类
    $api->post('/addSearchHotWord', 'UserController@addSearchHotWord');
    $api->get('/findBookBySecondCategory/{id}', 'NovelCategoryController@findBookBySecondCategory');
    $api->get('/bestSeller/{id}', 'NovelCategoryController@bestSeller');
    $api->get('/monthTicketBang/{id}', 'NovelCategoryController@monthTicketBang');
    $api->get('/bestSaleWithSecondCategory/{id}', 'NovelCategoryController@bestSaleWithSecondCategory');
    /*
     * 章节评论
     */
    //章节一级评论
    $api->post('/firstChapterComment','ChapterCommentController@firstChapterComment');
    //章节一级评论列表
    $api->get('/chapterCommentList/{id}','ChapterCommentController@chapterCommentList');
    //章节二级评论
    $api->post('/secondChapterComment','ChapterCommentController@secondChapterComment');
    //章节二级评论列表
    $api->get('/subChapterCommentList/{id}','ChapterCommentController@subChapterCommentList');

    /*
     * 用户给章节评论点赞
     */
    $api->post('/chapterLike','ChapterCommentController@chapterLike');

    /*
     * 小说评论
     */
    //小说一级评论
    $api->post('/firstNovelComment','NovelCommentController@firstNovelComment');
    //小说一级评论列表
    $api->get('/NovelCommentList/{id}','NovelCommentController@NovelCommentList');
    //小说二级评论
    $api->post('/secondNovelComment','NovelCommentController@secondNovelComment');
    //小说二级评论列表
    $api->get('/subNovelCommentList/{id}','NovelCommentController@subNovelCommentList');

    /*
     * 用户给小说评论点赞
     */
    $api->post('/novelLike','NovelCommentController@novelLike');

    //上传图片
    $api->post('/uploadImage', 'UploadController@storeImage');

    $api->get('/editorRecommend/{id}', 'NovelCategoryController@editorRecommend');

    $api->put('/userSignInformation', 'UserController@userSignInformation');

    $api->post('/subscriptionNovel', 'UserController@subscriptionNovel');

    $api->get('/exchangeSetings', 'ExchangeSettingController@index');


    //支付
    $api->any('/beanPay', 'ExchangeSettingController@beanPay');
    $api->any('/RechargeCallBack', 'ExchangeSettingController@RechargeCallBack');

    //测试支付

    $api->any('/testBeanPay', 'ExchangeSettingController@testBeanPay');

    $api->get('/userSubscription', 'UserController@userSubscription');




});