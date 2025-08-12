<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => __('auth.unauthenticated')
            ], 401);
        }

        // Check if user is an admin
        if ($request->user()->type !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => __('auth.unauthorized_admin')
            ], 403);
        }

        return $next($request);
    }
}
