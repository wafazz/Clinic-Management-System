<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PortalAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('portal_patient_id')) {
            return redirect()->route('portal.login');
        }

        return $next($request);
    }
}
