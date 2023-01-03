<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        //logger("==========RedirectIfAuthenticated==============");
        //logger($request);
        //logger("==========guard============");
       // logger($guard);


        if ($guard == "admin" && Auth::guard($guard)->check()) {
            return redirect('/admin');
        }
//        if (Auth::guard($guard)->check()) {
//            return redirect()->route('admin.login');
//        }
        if (Auth::guard($guard)->check()) {
            return redirect('/app');
        }

        return $next($request);
    }
}
