<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SmartSuggestionsService;

class SmartSuggestionsController extends Controller
{
    /**
     * Get suggestions for a field
     */
    public function getSuggestions(Request $request)
    {
        $fieldType = $request->get('field_type');
        $query = $request->get('query', '');
        $limit = $request->get('limit', 10);
        
        if (empty($fieldType)) {
            return response()->json(['suggestions' => []]);
        }
        
        $suggestions = SmartSuggestionsService::getSuggestions($fieldType, $query, $limit);
        
        return response()->json([
            'suggestions' => $suggestions,
            'field_type' => $fieldType,
            'query' => $query
        ]);
    }

    /**
     * Get route suggestions
     */
    public function getRouteSuggestions(Request $request)
    {
        $loadingPoint = $request->get('loading_point');
        $destination = $request->get('destination');
        $limit = $request->get('limit', 5);
        
        if (empty($loadingPoint) || empty($destination)) {
            return response()->json(['suggestions' => []]);
        }
        
        $suggestions = SmartSuggestionsService::getRouteSuggestions($loadingPoint, $destination, $limit);
        
        return response()->json([
            'suggestions' => $suggestions,
            'loading_point' => $loadingPoint,
            'destination' => $destination
        ]);
    }

    /**
     * Clear suggestions cache
     */
    public function clearCache()
    {
        SmartSuggestionsService::clearCache();
        
        return response()->json([
            'success' => true,
            'message' => 'Suggestions cache cleared successfully'
        ]);
    }
}
