<?php

namespace App\Http\Middleware;

use Closure;

class admin
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
        if ($request->user() && $request->user()->role != 'admin')
            {
                return response([
                    "success"   =>  false,
                    "message"   =>  "You are not authorized to access this resource"
                ]);
            }
        return $next($request);
    }
}
