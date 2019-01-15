<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Traits\ApiResponse;
use App\Traits\FractalHelpers;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ApiResponse, FractalHelpers;

    protected function myValidator(Request $request, array $rule, array $message) {
        $validator = Validator::make($request->all(), $rule, $message);
        if ($validator->fails()) {
            $errors =  $validator->errors();
            return $this->apiError($errors->first());
        }
        return [];
    }

    protected function myCodeCheck(Request $request) {
        $wechat = app('wechat.mini_program');
        $res = $wechat->auth->session($request->code?:'');
        if(isset($res['errcode']) && $res['errcode'] > 0) return ['success' => false, 'err' => $this->apiError($res['errmsg'], 500)];
        return ['success' => true, 'data' => $res];
    }
}
