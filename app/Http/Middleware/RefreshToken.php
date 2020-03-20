<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

// 注意，我们要继承的是 jwt 的 BaseMiddleware
class RefreshToken extends BaseMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @return mixed
     * @throws JWTException
     */
    public function handle($request, Closure $next)
    {
        //\Log::debug(1);
        if ($this->auth->parser()->setRequest($request)->hasToken()) {
            // 使用 try 包裹，以捕捉 token 过期所抛出的 TokenExpiredException  异常
            try {
                //\Log::debug(1);
                // 检测用户的登录状态，如果正常则通过
                //if (!$this->auth->parseToken()->authenticate()) {
                if ( ! $this->auth->parseToken()->check()) {
                    throw new TokenExpiredException('token 过期');
                }
                //\Log::debug(2, [auth($request->header('Auth-Guard'))->user()]);
                if ( ! auth($request->header('Auth-Guard'))->user()) {
                    throw new UnauthorizedHttpException('jwt-auth', '未登录');
                }

                $token = $this->auth->parseToken()->getToken();
                //\Log::debug(3, [$token]);
            } catch (TokenExpiredException $exception) {
                //\Log::debug('TokenExpiredException');
                // 此处捕获到了 token 过期所抛出的 TokenExpiredException 异常，我们在这里需要做的是刷新该用户的 token 并将它添加到响应头中
                try {
                    // 刷新用户的 token
                    $token = $this->auth->parseToken()->refresh();
                    //\Log::debug('TokenExpiredException1', [$token]);
                    // 使用一次性登录以保证此次请求的成功
                    auth($request->header('Auth-Guard'))->onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);
                } catch (JWTException $exception) {
                    //\Log::debug(4, [$exception->getMessage()]);
                    // 如果捕获到此异常，即代表 refresh 也过期了，用户无法刷新令牌，需要重新登录。
                    throw new UnauthorizedHttpException('jwt-auth', $exception->getMessage());
                }
            }
            //\Log::debug(5);
            // 在响应头中返回新的 token
            return $this->setAuthenticationHeader($next($request), $token);
        }

        //echo microtime(true)."--RefreshToken2--";

        return $next($request);
    }
}
