<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated and if their role is 'admin'
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request); // User is an admin, allow request to proceed
        }

        // If not an admin, return an "Unauthorized" error
        // return response()->json(['message' => 'Unauthorized'], 403);
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses!');
    }
}