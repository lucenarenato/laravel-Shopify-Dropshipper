<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPlan
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
        $shop = Auth::user();
        if( !$shop->plan_id ){
            return redirect("/create-plan");
        }else{
            return redirect("/import");
        }
//        return $next($request);
    }
}
