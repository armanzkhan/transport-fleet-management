<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'serial_number',
        'vrn',
        'owner_id',
        'driver_name',
        'driver_contact',
        'capacity',
        'engine_number',
        'chassis_number',
        'token_tax_expiry',
        'dip_chart_expiry',
        'induction_date',
        'tracker_name',
        'tracker_link',
        'tracker_id',
        'tracker_password',
        'tracker_expiry',
        'is_active'
    ];

    protected $casts = [
        'capacity' => 'decimal:2',
        'token_tax_expiry' => 'date',
        'dip_chart_expiry' => 'date',
        'induction_date' => 'date',
        'tracker_expiry' => 'date',
        'is_active' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(VehicleOwner::class, 'owner_id');
    }

    public function journeyVouchers(): HasMany
    {
        return $this->hasMany(JourneyVoucher::class);
    }

    public function cashBooks(): HasMany
    {
        return $this->hasMany(CashBook::class);
    }

    public function vehicleBills(): HasMany
    {
        return $this->hasMany(VehicleBill::class);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($vehicle) {
            if (empty($vehicle->serial_number)) {
                $lastSerial = static::max('serial_number');
                $nextNumber = 1;
                
                if ($lastSerial) {
                    $lastNumber = (int)substr($lastSerial, 3);
                    $nextNumber = $lastNumber + 1;
                }
                
                $vehicle->serial_number = 'VH-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function hasExpiringDocuments()
    {
        $fifteenDaysFromNow = now()->addDays(15);
        
        return $this->token_tax_expiry && $this->token_tax_expiry <= $fifteenDaysFromNow ||
               $this->dip_chart_expiry && $this->dip_chart_expiry <= $fifteenDaysFromNow ||
               $this->tracker_expiry && $this->tracker_expiry <= $fifteenDaysFromNow;
    }

    public function getExpiringDocuments()
    {
        $fifteenDaysFromNow = now()->addDays(15);
        $expiring = [];

        if ($this->token_tax_expiry && $this->token_tax_expiry <= $fifteenDaysFromNow) {
            $expiring[] = [
                'type' => 'Token Tax',
                'expiry_date' => $this->token_tax_expiry,
                'days_remaining' => now()->diffInDays($this->token_tax_expiry)
            ];
        }

        if ($this->dip_chart_expiry && $this->dip_chart_expiry <= $fifteenDaysFromNow) {
            $expiring[] = [
                'type' => 'Dip Chart',
                'expiry_date' => $this->dip_chart_expiry,
                'days_remaining' => now()->diffInDays($this->dip_chart_expiry)
            ];
        }

        if ($this->tracker_expiry && $this->tracker_expiry <= $fifteenDaysFromNow) {
            $expiring[] = [
                'type' => 'Tracker',
                'expiry_date' => $this->tracker_expiry,
                'days_remaining' => now()->diffInDays($this->tracker_expiry)
            ];
        }

        return $expiring;
    }
}
