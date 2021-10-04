<?php

namespace App\Http\Middleware\Backend;

use Closure;
use Illuminate\Support\Facades\Auth;

class Blog
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->roll_id == 1) {
            return $next($request);
        } elseif (Auth::check() && Auth::user()->roll_id == 2) {
            return $next($request);
        }

        return redirect('/login');
    }
}
