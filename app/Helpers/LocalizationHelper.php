<?php

if (!function_exists('trans_response')) {
    /**
     * Create a JSON response with localized message
     *
     * @param string $key
     * @param array $replace
     * @param int $status
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    function trans_response($key, $replace = [], $status = 200, $data = [])
    {
        $response = [
            'message' => __($key, $replace)
        ];

        if (!empty($data)) {
            $response = array_merge($response, $data);
        }

        return response()->json($response, $status);
    }
}

if (!function_exists('get_supported_locales')) {
    /**
     * Get supported locales
     *
     * @return array
     */
    function get_supported_locales()
    {
        return ['en', 'ar'];
    }
}

if (!function_exists('is_supported_locale')) {
    /**
     * Check if a locale is supported
     *
     * @param string $locale
     * @return bool
     */
    function is_supported_locale($locale)
    {
        return in_array($locale, get_supported_locales());
    }
}
