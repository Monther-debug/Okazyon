<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocalizationController extends Controller
{
    /**
     * Get current locale information
     */
    public function getLocale()
    {
        return response()->json([
            'current_locale' => App::getLocale(),
            'supported_locales' => get_supported_locales(),
            'test_message' => __('auth.login_successful')
        ]);
    }

    /**
     * Set locale for testing
     */
    public function setLocale(Request $request)
    {
        $locale = $request->input('locale', 'en');

        if (!is_supported_locale($locale)) {
            return response()->json(['error' => 'Unsupported locale'], 400);
        }

        App::setLocale($locale);

        return response()->json([
            'message' => 'Locale set successfully',
            'current_locale' => App::getLocale(),
            'test_message' => __('auth.login_successful')
        ]);
    }
}
