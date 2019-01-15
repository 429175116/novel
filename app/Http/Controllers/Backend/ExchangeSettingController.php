<?php

namespace App\Http\Controllers\Backend;


use App\Model\ExchangeSetting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExchangeSettingController extends Controller
{
    //
    /**
     * 添加兑换信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request) {

        $input = $request->all();
        $exchange_setting = ExchangeSetting::where('amount', $input['amount'])->first();
        if(!empty($exchange_setting)) return $this->apiError('已添加改配置，请勿重新添加');
        $exchange_setting = new ExchangeSetting();
        $exchange_setting->amount = $input['amount'];
        $exchange_setting->bi_count = $input['bi_count'];
        $exchange_setting->bi_gift_count = $input['bi_gift_count'];
        $exchange_setting->save();
        unset($exchange_setting);
        return $this->apiSuccess();
    }

    /**
     * 修改兑换信息
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, $id) {
        $exchange_setting = ExchangeSetting::findorfail($id);
        $input = $request->all();
        $exchange_setting->amount = $input['amount'];
        $exchange_setting->bi_count = $input['bi_count'];
        $exchange_setting->bi_gift_count = $input['bi_gift_count'];
        $exchange_setting->save();
        unset($exchange_setting);
        return $this->apiSuccess();
    }

    public function delete($id) {
        $exchange_setting = ExchangeSetting::findorfail($id);
        $exchange_setting->delete();
        return $this->apiSuccess();
    }

    public function index() {
        $exchange_settings = ExchangeSetting::get();
        $data = [
            'exchange_settings' => $exchange_settings
        ];
        return $this->apiSuccess($data);
    }
}





