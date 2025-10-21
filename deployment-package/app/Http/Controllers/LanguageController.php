<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LanguageService;

class LanguageController extends Controller
{
    /**
     * Switch language
     */
    public function switch(Request $request)
    {
        $language = $request->get('language');
        
        if (LanguageService::setLanguage($language)) {
            return response()->json([
                'success' => true,
                'language' => $language,
                'direction' => LanguageService::getDirection(),
                'message' => 'Language switched successfully'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Invalid language selected'
        ], 400);
    }

    /**
     * Get current language
     */
    public function current()
    {
        return response()->json([
            'language' => LanguageService::getCurrentLanguage(),
            'direction' => LanguageService::getDirection(),
            'available_languages' => LanguageService::getAvailableLanguages()
        ]);
    }

    /**
     * Get translations for current language
     */
    public function translations()
    {
        $language = LanguageService::getCurrentLanguage();
        $translations = LanguageService::getTranslations($language);
        
        return response()->json([
            'language' => $language,
            'direction' => LanguageService::getDirection(),
            'translations' => $translations
        ]);
    }
}
