<?php
namespace App\Traits;
trait ApiResponse
{
    /**
     * 11001 余额不足
     * 11002 购买超过上限
     * 10002 过期或未登录
     * 11006 超过购买上限
     *
     *
     *
     *
     * @param array $params
     * @return \Illuminate\Http\JsonResponse
     */

    protected function apiSuccess($params = [])
    {
        $data = [
            'success' => true,
            'errcode' => 0,
            'errmsg' => '',
        ];
        $data = array_merge($data, $params);
        return response()->json($data);
    }

    protected function apiError($errmsg, $errcode=500, $httpStatusCode=200)
    {
        $data = [
            'success' => false,
            'errcode' => $errcode,
            'errmsg' => $errmsg,
        ];
        return response()->json($data, $httpStatusCode);
    }
}