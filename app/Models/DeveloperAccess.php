<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeveloperAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'developer_name',
        'developer_email',
        'access_type',
        'permissions',
        'start_date',
        'end_date',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'revoked_by',
        'revoked_at',
        'created_by'
    ];

    protected $casts = [
        'permissions' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'approved_at' => 'datetime',
        'revoked_at' => 'datetime'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_REVOKED = 'revoked';

    const ACCESS_TYPES = [
        'read_only' => 'Read Only',
        'limited_write' => 'Limited Write',
        'full_access' => 'Full Access',
        'emergency' => 'Emergency Access'
    ];

    /**
     * Get the user who created the access request
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the access
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who revoked the access
     */
    public function revoker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    /**
     * Scope for active access
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                    ->where('start_date', '<=', now())
                    ->where(function($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    /**
     * Scope for expired access
     */
    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->where('status', self::STATUS_EXPIRED)
              ->orWhere(function($q2) {
                  $q2->where('status', self::STATUS_ACTIVE)
                     ->where('end_date', '<', now());
              });
        });
    }

    /**
     * Scope for pending access
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Check if access is currently active
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE 
               && $this->start_date <= now() 
               && ($this->end_date === null || $this->end_date >= now());
    }

    /**
     * Check if access is expired
     */
    public function isExpired()
    {
        return $this->end_date !== null && $this->end_date < now();
    }

    /**
     * Get access type label
     */
    public function getAccessTypeLabelAttribute()
    {
        return self::ACCESS_TYPES[$this->access_type] ?? 'Unknown';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_APPROVED => 'bg-info',
            self::STATUS_ACTIVE => 'bg-success',
            self::STATUS_EXPIRED => 'bg-secondary',
            self::STATUS_REVOKED => 'bg-danger',
            default => 'bg-light'
        };
    }

    /**
     * Get remaining time
     */
    public function getRemainingTimeAttribute()
    {
        if ($this->end_date === null) {
            return 'No expiry';
        }
        
        $remaining = $this->end_date->diffInDays(now());
        return $remaining > 0 ? "{$remaining} days" : 'Expired';
    }

    /**
     * Auto-expire access
     */
    public function autoExpire()
    {
        if ($this->isExpired() && $this->status === self::STATUS_ACTIVE) {
            $this->update(['status' => self::STATUS_EXPIRED]);
        }
    }
}
