<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LocumPortalAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('locum_id')) {
            return redirect()->route('locum-portal.login');
        }
        return $next($request);
    }
}
