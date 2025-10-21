<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashBook extends Model
{
    protected $fillable = [
        'cash_book_number',
        'entry_date',
        'transaction_type',
        'transaction_number',
        'account_id',
        'vehicle_id',
        'payment_type',
        'description',
        'amount',
        'previous_day_balance',
        'total_cash_in_hand',
        'created_by'
    ];

    protected $casts = [
        'entry_date' => 'date',
        'amount' => 'decimal:2',
        'previous_day_balance' => 'decimal:2',
        'total_cash_in_hand' => 'decimal:2',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($cashBook) {
            if (empty($cashBook->cash_book_number)) {
                $date = $cashBook->entry_date ? $cashBook->entry_date->format('Ymd') : now()->format('Ymd');
                $lastEntry = static::whereDate('entry_date', $cashBook->entry_date ?: now())->max('cash_book_number');
                $nextNumber = 1;
                
                if ($lastEntry) {
                    $lastNumber = (int)substr($lastEntry, -4);
                    $nextNumber = $lastNumber + 1;
                }
                
                $cashBook->cash_book_number = 'CB-' . $date . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
            
            if (empty($cashBook->transaction_number)) {
                $lastTransaction = static::max('transaction_number');
                $nextNumber = 1;
                
                if ($lastTransaction) {
                    $lastNumber = (int)substr($lastTransaction, 4);
                    $nextNumber = $lastNumber + 1;
                }
                
                $cashBook->transaction_number = 'TRX-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public static function getPreviousDayBalance($date)
    {
        $previousDay = \Carbon\Carbon::parse($date)->subDay();
        
        // Get the last entry from the previous day (either receive or payment)
        $lastEntry = self::where('entry_date', '<=', $previousDay->format('Y-m-d'))
            ->orderBy('entry_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastEntry) {
            return $lastEntry->total_cash_in_hand;
        }
        
        // If no previous entries, return 0
        return 0;
    }

    public static function calculateTotalCashInHand($date, $receivedAmount, $previousBalance)
    {
        return $receivedAmount + $previousBalance;
    }
}
