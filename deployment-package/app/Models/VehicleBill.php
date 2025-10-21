<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleBill extends Model
{
    protected $fillable = [
        'bill_number',
        'vehicle_id',
        'billing_month',
        'previous_bill_balance',
        'total_freight',
        'total_advance',
        'total_expense',
        'total_shortage',
        'gross_profit',
        'net_profit',
        'total_vehicle_balance',
        'status',
        'is_finalized',
        'created_by'
    ];

    protected $casts = [
        'previous_bill_balance' => 'decimal:2',
        'total_freight' => 'decimal:2',
        'total_advance' => 'decimal:2',
        'total_expense' => 'decimal:2',
        'total_shortage' => 'decimal:2',
        'gross_profit' => 'decimal:2',
        'net_profit' => 'decimal:2',
        'total_vehicle_balance' => 'decimal:2',
        'is_finalized' => 'boolean',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getBillNumberAttribute()
    {
        if (!$this->attributes['bill_number']) {
            $this->attributes['bill_number'] = 'BILL-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
            $this->save();
        }
        return $this->attributes['bill_number'];
    }

    public function calculateTotals()
    {
        $this->gross_profit = $this->total_freight - $this->total_advance - $this->total_expense;
        $this->net_profit = $this->gross_profit - $this->total_shortage;
        $this->total_vehicle_balance = $this->previous_bill_balance + $this->net_profit;
        $this->save();
    }
}
