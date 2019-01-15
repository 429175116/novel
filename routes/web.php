<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['namespace' => 'Backend', 'prefix' => '/backend'], function (){
    Route::post('/login', 'AuthController@login');

    /**
     * 小说分类相关
     */
    Route::post('/createFirstCategory', 'NovelCategoryController@createFirstCategory');
    Route::get('/firstCategory', 'NovelCategoryController@firstCategory');
    Route::post('/createSecondCategory/{parent}', 'NovelCategoryController@createSecondCategory');
    Route::get('/secondCategory/{parent}', 'NovelCategoryController@secondCategory');
    Route::get('/categoryForCascader', 'NovelCategoryController@categoryForCascader');

    /**
     * 小说相关
     */
    Route::post('/createNovel', 'AuthorController@createNovel')->middleware('jwt');
    Route::post('/createNovelForAll', 'NovelController@createNovelForAll');
    Route::post('/addChapterForAll', 'NovelController@addChapterForAll');

    Route::get('/myNovel', 'AuthorController@myNovel')->middleware('jwt');
    Route::post('/addChapter', 'AuthorController@addChapter')->middleware('jwt');//->middleware('jwt')
    Route::put('/editChapter/{novel_chapter}', 'AuthorController@editChapter')->middleware('jwt');//->middleware('jwt')
    Route::get('/novelDetail/{novel}', 'NovelController@detail');

    /**
     * 作家相关
     */
    Route::post('/addTag/{novel_id}', 'AuthorController@addTag')->middleware('jwt');
    Route::delete('/deletetag/{novel_id}', 'AuthorController@deletetag')->middleware('jwt');
    Route::get('/authInformation', 'AuthController@authInformation')->middleware('jwt');
    Route::post('/addEditorRecommend/{novel_id}', 'AuthorController@addEditorRecommend')->middleware('jwt');


    //上传图片
    Route::post('/uploadImage', 'UploadController@storeImage');
    Route::post('/storeTxt', 'UploadController@storeTxt');


    //添加配置
    Route::post('/createExchangeSetting', 'ExchangeSettingController@create');
    Route::put('/editExchangeSetting/{id}', 'ExchangeSettingController@edit');
    Route::get('/exchangeSettings', 'ExchangeSettingController@index');




});
