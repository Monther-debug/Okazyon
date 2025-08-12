<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for Accept-Language header first
        $locale = $request->header('Accept-Language');

        // Check for locale in request parameters
        if (!$locale && $request->has('locale')) {
            $locale = $request->get('locale');
        }

        // Check for locale in query parameters
        if (!$locale && $request->query('locale')) {
            $locale = $request->query('locale');
        }

        // Set default to English if no locale is provided
        if (!$locale) {
            $locale = 'en';
        }

        // Validate locale (only allow 'en' and 'ar')
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
