<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tariff extends Model
{
    protected $fillable = [
        'tariff_number',
        'from_date',
        'to_date',
        'carriage_name',
        'company',
        'loading_point',
        'destination',
        'company_freight_rate',
        'vehicle_freight_rate',
        'company_shortage_rate',
        'vehicle_shortage_rate',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'company_freight_rate' => 'decimal:2',
        'vehicle_freight_rate' => 'decimal:2',
        'company_shortage_rate' => 'decimal:2',
        'vehicle_shortage_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTariffNumberAttribute()
    {
        return $this->attributes['tariff_number'];
    }

    public static function getApplicableTariff($carriageName, $company, $loadingPoint, $destination, $date)
    {
        return self::where('carriage_name', $carriageName)
            ->where('company', $company)
            ->where('loading_point', $loadingPoint)
            ->where('destination', $destination)
            ->where('from_date', '<=', $date)
            ->where('to_date', '>=', $date)
            ->where('is_active', true)
            ->first();
    }
}
