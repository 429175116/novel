<?php

namespace App\Http\Middleware;

use Closure;

class RefreshToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 检查此次请求中是否带有 token，如果没有则抛出异常。
        try {
            $user = auth()->guard('api')->userOrFail();
            if(!$user) {
                return response()->json(['message' => 'jwt 无效'], 401);
            }
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json(['message' => 'jwt 无效'], 401);
        }
        return $next($request);
    }
}
