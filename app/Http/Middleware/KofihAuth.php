<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class KofihAuth
{
    /**
     * Kofih 코드 인증 미들웨어
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 세션에서 kofih 인증 확인
        if (!$request->session()->has('kofih_authenticated')) {
            return redirect('/kofih/login');
        }

        return $next($request);
    }
}