<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in AND user role is admin
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request); // Allow access
        }

        // If not admin, block access with 403 error
        abort(403, 'Unauthorized access! Only admins allowed.');
    }
}