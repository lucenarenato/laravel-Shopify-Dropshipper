<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$guard = null)
    {

       // logger("=============IsAdmin::Middleware===========");

       // logger($request);

        //logger("==========IsAdmin::guard============");
       // logger($guard);


        if ($guard == "admin" && Auth::guard($guard)->check()) {
            return redirect('/admin');
        }


        return redirect('/login/admin');
    }
}
