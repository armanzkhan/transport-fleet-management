<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecondaryJourneyVoucherEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'secondary_journey_voucher_id',
        'vrn',
        'invoice_number',
        'loading_point',
        'destination',
        'product',
        'rate',
        'load_quantity',
        'freight',
        'shortage_quantity',
        'shortage_amount',
        'company_deduction',
        'vehicle_commission',
        'net_amount',
        'pr04'
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'load_quantity' => 'decimal:2',
        'freight' => 'decimal:2',
        'shortage_quantity' => 'decimal:2',
        'shortage_amount' => 'decimal:2',
        'company_deduction' => 'decimal:2',
        'vehicle_commission' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'pr04' => 'boolean'
    ];

    /**
     * Get the secondary journey voucher that owns the entry
     */
    public function secondaryJourneyVoucher(): BelongsTo
    {
        return $this->belongsTo(SecondaryJourneyVoucher::class);
    }

    /**
     * Get the vehicle for this entry
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vrn', 'vrn');
    }

    /**
     * Get commission amount for this entry
     */
    public function getCommissionAmountAttribute()
    {
        $commission = $this->vehicle_commission - $this->company_deduction;
        return max(0, $commission); // Return 0 if negative
    }

    /**
     * Get shortage rate for this entry
     */
    public function getShortageRateAttribute()
    {
        if ($this->shortage_quantity > 0) {
            return $this->shortage_amount / $this->shortage_quantity;
        }
        return 0;
    }

    /**
     * Scope for PR04 entries
     */
    public function scopePr04($query)
    {
        return $query->where('pr04', true);
    }

    /**
     * Scope for regular entries
     */
    public function scopeRegular($query)
    {
        return $query->where('pr04', false);
    }

    /**
     * Scope for filtering by VRN
     */
    public function scopeByVrn($query, $vrn)
    {
        return $query->where('vrn', $vrn);
    }

    /**
     * Scope for filtering by route
     */
    public function scopeByRoute($query, $loadingPoint, $destination)
    {
        return $query->where('loading_point', $loadingPoint)
                    ->where('destination', $destination);
    }

    /**
     * Scope for filtering by product
     */
    public function scopeByProduct($query, $product)
    {
        return $query->where('product', $product);
    }
}
