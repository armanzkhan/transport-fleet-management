<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JourneyVoucher extends Model
{
    protected $fillable = [
        'journey_number',
        'journey_date',
        'vehicle_id',
        'carriage_name',
        'loading_point',
        'loading_point_urdu',
        'capacity',
        'company_freight_rate',
        'vehicle_freight_rate',
        'shortage_quantity',
        'shortage_rate',
        'company_deduction_percentage',
        'vehicle_deduction_percentage',
        'billing_month',
        'product',
        'product_urdu',
        'invoice_number',
        'destination',
        'destination_urdu',
        'company',
        'decant_capacity',
        'is_direct_bill',
        'journey_type',
        'freight_amount',
        'shortage_amount',
        'commission_amount',
        'total_amount',
        'is_processed',
        'is_billed',
        'created_by'
    ];

    protected $casts = [
        'journey_date' => 'date',
        'capacity' => 'decimal:2',
        'company_freight_rate' => 'decimal:2',
        'vehicle_freight_rate' => 'decimal:2',
        'shortage_quantity' => 'decimal:2',
        'shortage_rate' => 'decimal:2',
        'company_deduction_percentage' => 'decimal:2',
        'vehicle_deduction_percentage' => 'decimal:2',
        'decant_capacity' => 'decimal:2',
        'is_direct_bill' => 'boolean',
        'freight_amount' => 'decimal:2',
        'shortage_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_processed' => 'boolean',
        'is_billed' => 'boolean',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getJourneyNumberAttribute()
    {
        if (!$this->attributes['journey_number']) {
            $this->attributes['journey_number'] = 'JV-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
            $this->save();
        }
        return $this->attributes['journey_number'];
    }

    public function calculateAmounts()
    {
        $decantCapacity = $this->decant_capacity ?? $this->capacity;
        $vehicleRate = $this->vehicle_freight_rate ?? $this->company_freight_rate;
        
        // Calculate freight amounts
        $companyFreight = $decantCapacity * $this->company_freight_rate;
        $vehicleFreight = $decantCapacity * $vehicleRate;
        
        // Calculate shortage amount
        $shortageAmount = $this->shortage_quantity * $this->shortage_rate;
        
        // Calculate deductions
        $companyDeduction = ($companyFreight * $this->company_deduction_percentage) / 100;
        $vehicleDeduction = ($vehicleFreight * $this->vehicle_deduction_percentage) / 100;
        
        // Calculate commission (difference between vehicle and company deductions)
        $commissionAmount = $vehicleDeduction - $companyDeduction;
        
        // Calculate total
        $totalAmount = $vehicleFreight - $shortageAmount - $vehicleDeduction;
        
        $this->update([
            'freight_amount' => $vehicleFreight,
            'shortage_amount' => $shortageAmount,
            'commission_amount' => max(0, $commissionAmount), // Commission cannot be negative
            'total_amount' => $totalAmount
        ]);
        
        return $this;
    }

    public function getFreightDifferenceIncome()
    {
        $decantCapacity = $this->decant_capacity ?? $this->capacity;
        $companyFreight = $decantCapacity * $this->company_freight_rate;
        $vehicleFreight = $decantCapacity * ($this->vehicle_freight_rate ?? $this->company_freight_rate);
        
        return $companyFreight - $vehicleFreight;
    }
}
