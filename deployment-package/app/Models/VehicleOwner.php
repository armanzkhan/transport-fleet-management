<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleOwner extends Model
{
    protected $fillable = [
        'serial_number',
        'name',
        'cnic',
        'contact_number',
        'address',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'owner_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vehicleOwner) {
            if (empty($vehicleOwner->serial_number)) {
                // Get the next number based on existing records
                $lastOwner = static::orderBy('id', 'desc')->first();
                $nextNumber = $lastOwner ? $lastOwner->id + 1 : 1;
                $vehicleOwner->serial_number = 'OWN-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
