<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectAdminGuests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If URL starts with admin/ and user is not logged in, send to admin login
        if ($request->is('admin/*') && !auth()->check()) {
            return redirect()->route('admin.signin');
        }

        return $next($request);
    }
}
