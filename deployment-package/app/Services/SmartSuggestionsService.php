<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\VehicleOwner;
use App\Models\MasterData;
use App\Models\ChartOfAccount;
use App\Models\JourneyVoucher;
use App\Models\SecondaryJourneyVoucher;
use Illuminate\Support\Facades\Cache;

class SmartSuggestionsService
{
    /**
     * Get suggestions for any field type
     */
    public static function getSuggestions($fieldType, $query, $limit = 10)
    {
        $cacheKey = "suggestions_{$fieldType}_{$query}_{$limit}";
        
        return Cache::remember($cacheKey, 300, function() use ($fieldType, $query, $limit) {
            return match($fieldType) {
                'vrn' => self::getVrnSuggestions($query, $limit),
                'owner' => self::getOwnerSuggestions($query, $limit),
                'driver' => self::getDriverSuggestions($query, $limit),
                'company' => self::getCompanySuggestions($query, $limit),
                'loading_point' => self::getLoadingPointSuggestions($query, $limit),
                'destination' => self::getDestinationSuggestions($query, $limit),
                'product' => self::getProductSuggestions($query, $limit),
                'account' => self::getAccountSuggestions($query, $limit),
                'invoice' => self::getInvoiceSuggestions($query, $limit),
                'contractor' => self::getContractorSuggestions($query, $limit),
                default => []
            };
        });
    }

    /**
     * Get VRN suggestions
     */
    private static function getVrnSuggestions($query, $limit)
    {
        return Vehicle::where('vrn', 'like', "%{$query}%")
            ->where('is_active', true)
            ->with('owner')
            ->limit($limit)
            ->get()
            ->map(function($vehicle) {
                return [
                    'value' => $vehicle->vrn,
                    'label' => $vehicle->vrn,
                    'description' => $vehicle->owner->name ?? 'No Owner',
                    'type' => 'vrn',
                    'data' => [
                        'owner' => $vehicle->owner->name ?? null,
                        'capacity' => $vehicle->capacity ?? null,
                        'driver' => $vehicle->driver_name ?? null
                    ]
                ];
            });
    }

    /**
     * Get owner suggestions
     */
    private static function getOwnerSuggestions($query, $limit)
    {
        return VehicleOwner::where('name', 'like', "%{$query}%")
            ->limit($limit)
            ->get()
            ->map(function($owner) {
                return [
                    'value' => $owner->name,
                    'label' => $owner->name,
                    'description' => $owner->contact_number ?? 'No Contact',
                    'type' => 'owner',
                    'data' => [
                        'cnic' => $owner->cnic_number ?? null,
                        'contact' => $owner->contact_number ?? null
                    ]
                ];
            });
    }

    /**
     * Get driver suggestions
     */
    private static function getDriverSuggestions($query, $limit)
    {
        return Vehicle::where('driver_name', 'like', "%{$query}%")
            ->whereNotNull('driver_name')
            ->where('is_active', true)
            ->limit($limit)
            ->get()
            ->map(function($vehicle) {
                return [
                    'value' => $vehicle->driver_name,
                    'label' => $vehicle->driver_name,
                    'description' => $vehicle->vrn . ' - ' . ($vehicle->owner->name ?? 'No Owner'),
                    'type' => 'driver',
                    'data' => [
                        'vrn' => $vehicle->vrn,
                        'owner' => $vehicle->owner->name ?? null,
                        'contact' => $vehicle->driver_contact_number ?? null
                    ]
                ];
            });
    }

    /**
     * Get company suggestions
     */
    private static function getCompanySuggestions($query, $limit)
    {
        return MasterData::where('type', 'company')
            ->where('name', 'like', "%{$query}%")
            ->where('is_active', true)
            ->limit($limit)
            ->get()
            ->map(function($company) {
                return [
                    'value' => $company->name,
                    'label' => $company->name,
                    'description' => 'Company',
                    'type' => 'company'
                ];
            });
    }

    /**
     * Get loading point suggestions
     */
    private static function getLoadingPointSuggestions($query, $limit)
    {
        return MasterData::where('type', 'loading_point')
            ->where('name', 'like', "%{$query}%")
            ->where('is_active', true)
            ->limit($limit)
            ->get()
            ->map(function($point) {
                return [
                    'value' => $point->name,
                    'label' => $point->name,
                    'description' => 'Loading Point',
                    'type' => 'loading_point'
                ];
            });
    }

    /**
     * Get destination suggestions
     */
    private static function getDestinationSuggestions($query, $limit)
    {
        return MasterData::where('type', 'destination')
            ->where('name', 'like', "%{$query}%")
            ->where('is_active', true)
            ->limit($limit)
            ->get()
            ->map(function($destination) {
                return [
                    'value' => $destination->name,
                    'label' => $destination->name,
                    'description' => 'Destination',
                    'type' => 'destination'
                ];
            });
    }

    /**
     * Get product suggestions
     */
    private static function getProductSuggestions($query, $limit)
    {
        return MasterData::where('type', 'product')
            ->where('name', 'like', "%{$query}%")
            ->where('is_active', true)
            ->limit($limit)
            ->get()
            ->map(function($product) {
                return [
                    'value' => $product->name,
                    'label' => $product->name,
                    'description' => 'Product',
                    'type' => 'product'
                ];
            });
    }

    /**
     * Get account suggestions
     */
    private static function getAccountSuggestions($query, $limit)
    {
        return ChartOfAccount::where('account_name', 'like', "%{$query}%")
            ->where('is_active', true)
            ->limit($limit)
            ->get()
            ->map(function($account) {
                return [
                    'value' => $account->account_name,
                    'label' => $account->account_name,
                    'description' => $account->account_type ?? 'Account',
                    'type' => 'account',
                    'data' => [
                        'account_code' => $account->account_code ?? null,
                        'account_type' => $account->account_type ?? null
                    ]
                ];
            });
    }

    /**
     * Get invoice suggestions
     */
    private static function getInvoiceSuggestions($query, $limit)
    {
        $primaryInvoices = JourneyVoucher::where('invoice_number', 'like', "%{$query}%")
            ->distinct()
            ->pluck('invoice_number')
            ->take($limit / 2);
            
        $secondaryInvoices = SecondaryJourneyVoucher::whereHas('entries', function($q) use ($query) {
            $q->where('invoice_number', 'like', "%{$query}%");
        })
        ->with('entries')
        ->get()
        ->pluck('entries')
        ->flatten()
        ->pluck('invoice_number')
        ->unique()
        ->take($limit / 2);
        
        return $primaryInvoices->merge($secondaryInvoices)->unique()->map(function($invoice) {
            return [
                'value' => $invoice,
                'label' => $invoice,
                'description' => 'Invoice Number',
                'type' => 'invoice'
            ];
        });
    }

    /**
     * Get contractor suggestions
     */
    private static function getContractorSuggestions($query, $limit)
    {
        return SecondaryJourneyVoucher::where('contractor_name', 'like', "%{$query}%")
            ->distinct()
            ->pluck('contractor_name')
            ->take($limit)
            ->map(function($contractor) {
                return [
                    'value' => $contractor,
                    'label' => $contractor,
                    'description' => 'Contractor',
                    'type' => 'contractor'
                ];
            });
    }

    /**
     * Get route suggestions (loading point + destination)
     */
    public static function getRouteSuggestions($loadingPoint, $destination, $limit = 5)
    {
        $cacheKey = "route_suggestions_{$loadingPoint}_{$destination}_{$limit}";
        
        return Cache::remember($cacheKey, 600, function() use ($loadingPoint, $destination, $limit) {
            // Get recent rates for this route
            $primaryRates = JourneyVoucher::where('loading_point', $loadingPoint)
                ->where('destination', $destination)
                ->whereNotNull('company_freight_rate')
                ->orderBy('journey_date', 'desc')
                ->limit($limit)
                ->pluck('company_freight_rate')
                ->unique();
                
            $secondaryRates = SecondaryJourneyVoucher::whereHas('entries', function($q) use ($loadingPoint, $destination) {
                $q->where('loading_point', $loadingPoint)
                  ->where('destination', $destination);
            })
            ->with('entries')
            ->get()
            ->pluck('entries')
            ->flatten()
            ->pluck('rate')
            ->unique();
            
            $allRates = $primaryRates->merge($secondaryRates)->unique()->sort()->values();
            
            return [
                'rates' => $allRates,
                'suggested_rate' => $allRates->avg(),
                'min_rate' => $allRates->min(),
                'max_rate' => $allRates->max()
            ];
        });
    }

    /**
     * Clear suggestions cache
     */
    public static function clearCache()
    {
        Cache::forget('suggestions_*');
    }
}
