<?php

if (!function_exists('__t')) {
    /**
     * Translation helper function
     */
    function __t($key, $default = null)
    {
        return \App\Services\LanguageService::translate($key, $default);
    }
}

if (!function_exists('getCurrentLanguage')) {
    /**
     * Get current language
     */
    function getCurrentLanguage()
    {
        return \App\Services\LanguageService::getCurrentLanguage();
    }
}

if (!function_exists('getLanguageDirection')) {
    /**
     * Get language direction
     */
    function getLanguageDirection()
    {
        return \App\Services\LanguageService::getDirection();
    }
}
