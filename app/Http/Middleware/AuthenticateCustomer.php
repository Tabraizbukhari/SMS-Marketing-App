<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class AuthenticateCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(isset(Auth::user()->getUserData->register_as) && Auth::user()->getUserData->register_as == 'customer'){
            return redirect()->route(Auth::user()->type.'.dashboard');     
        }
        return $next($request);
    }
}
