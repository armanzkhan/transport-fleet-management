<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecondaryJourneyVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'journey_number',
        'contractor_name',
        'company',
        'journey_date',
        'total_freight',
        'total_shortage',
        'total_company_deduction',
        'total_vehicle_commission',
        'net_amount',
        'created_by'
    ];

    protected $casts = [
        'journey_date' => 'date',
        'total_freight' => 'decimal:2',
        'total_shortage' => 'decimal:2',
        'total_company_deduction' => 'decimal:2',
        'total_vehicle_commission' => 'decimal:2',
        'net_amount' => 'decimal:2'
    ];

    /**
     * Get the entries for the secondary journey voucher
     */
    public function entries(): HasMany
    {
        return $this->hasMany(SecondaryJourneyVoucherEntry::class);
    }

    /**
     * Get the user who created the voucher
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get commission amount (Vehicle Commission - Company Deduction)
     */
    public function getCommissionAmountAttribute()
    {
        $commission = $this->total_vehicle_commission - $this->total_company_deduction;
        return max(0, $commission); // Return 0 if negative
    }

    /**
     * Get total entries count
     */
    public function getEntriesCountAttribute()
    {
        return $this->entries()->count();
    }

    /**
     * Get PR04 entries count
     */
    public function getPr04EntriesCountAttribute()
    {
        return $this->entries()->where('pr04', true)->count();
    }

    /**
     * Get regular entries count
     */
    public function getRegularEntriesCountAttribute()
    {
        return $this->entries()->where('pr04', false)->count();
    }

    /**
     * Scope for filtering by company
     */
    public function scopeByCompany($query, $company)
    {
        return $query->where('company', $company);
    }

    /**
     * Scope for filtering by contractor
     */
    public function scopeByContractor($query, $contractor)
    {
        return $query->where('contractor_name', $contractor);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('journey_date', [$startDate, $endDate]);
    }

    /**
     * Scope for PR04 entries only
     */
    public function scopePr04Only($query)
    {
        return $query->whereHas('entries', function($q) {
            $q->where('pr04', true);
        });
    }

    /**
     * Scope for regular entries only
     */
    public function scopeRegularOnly($query)
    {
        return $query->whereHas('entries', function($q) {
            $q->where('pr04', false);
        });
    }
}
