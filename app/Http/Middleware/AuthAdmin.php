<?php

namespace App\Http\Middleware;

use App\Exceptions\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;

class AuthAdmin extends BaseAuthenticate
{

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     *
     * @throws AuthenticationException
     */
    protected function authenticate($request, array $guards)
    {
        if ($this->auth->guard('admin')->check()) {
            return $this->auth->shouldUse('admin');
        }

        throw new AuthenticationException('登录超时，请重新登录', $guards, $this->redirectTo($request));
    }
}
