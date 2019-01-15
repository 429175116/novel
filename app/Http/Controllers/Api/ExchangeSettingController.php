<?php

namespace App\Http\Controllers\Api;

use App\Model\ExchangeSetting;
use App\Model\User;
use App\Service\UserService;
use App\Transformers\ExchangeSettingTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExchangeSettingController extends Controller
{
    //

    public function index() {
        $exchange_settings = ExchangeSetting::get();
        $data = [
            'exchange_settings' => $exchange_settings
        ];
        return $this->apiSuccess($data);
    }

    //支付
    public function beanPay(Request $request) {
        //获取用户信息 身份验证
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) return $this->apiError($return_data['errmsg']);
        $user = $return_data['data'];

        $out_trade_no = date('Y-m-d').uniqid();
        //充值金额
        $id = $request->input('id');
        $exchange_setting = ExchangeSetting::findorfail($id);
        $pay_amount = $exchange_setting['amount'];

        //小程序的app
        $app = app('wechat.payment');

        $result = $app->order->unify([
            'body' => '用户充值',
            'out_trade_no' => $out_trade_no,
            'total_fee' => $pay_amount * 100,
            'notify_url' => config('app.url') . '/api/RechargeCallBack', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type' => 'JSAPI',
            'openid' => $user->open_id,
            'attach' => $exchange_setting->id
        ]);

        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
            $jssdk = $app->jssdk;
            $config = $jssdk->sdkConfig($result['prepay_id']); // 返回数组
            $data['data'] = [
                'config' => $config
            ];
//        //唤起输入密码信息
            return $this->apiSuccess($data);
        }
    }

    //充值活力豆成功回调
    public function RechargeCallBack() {
        \Log::info('request arrived.');
        $wechat = app('wechat.payment');
        $response = $wechat->handlePaidNotify(function ($message, $fail) use ($wechat){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = $message['out_trade_no'];
            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if (array_get($message, 'result_code') === 'SUCCESS') {
                    \DB::transaction(function () use ($message){
                        $user = User::where('open_id', $message['openid'])->first();
                        $exchange_setting_id = $message['attach'];
                        $exchange_setting =ExchangeSetting::query()->findorfail($exchange_setting_id);
                        $user->bi_count = $user->bi_count * 1 + $exchange_setting['bi_count'] * 1 + $exchange_setting['bi_gift_count'] *1;
                        $user->save();

                    });
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }
            return true; // 返回处理完成
        });

        return $response;
    }

    //支付
    public function testBeanPay(Request $request) {
        //获取用户信息 身份验证
        $return_data = UserService::getUser($request->header('AuthrizeOpenId'));
        if($return_data['errcode'] > 0) return $this->apiError($return_data['errmsg']);
        $user = $return_data['data'];
        $out_trade_no = date('Y-m-d').uniqid();
        //充值金额
        $id = $request->input('id');
        $exchange_setting = ExchangeSetting::findorfail($id);
        $user->bi_count = $user->bi_count * 1 + $exchange_setting['bi_count'] * 1 + $exchange_setting['bi_gift_count'] * 1;
        $user->save();
        return $this->apiSuccess();
    }

}
