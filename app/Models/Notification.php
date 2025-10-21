<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'priority',
        'vehicle_id',
        'expiry_date',
        'days_left',
        'is_read',
        'read_at',
        'created_by'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'read_at' => 'datetime',
        'is_read' => 'boolean',
        'days_left' => 'integer'
    ];

    /**
     * Get the vehicle that owns the notification
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the user who created the notification
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for high priority notifications
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    /**
     * Scope for notifications by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeClassAttribute()
    {
        return match($this->priority) {
            'high' => 'bg-danger',
            'medium' => 'bg-warning',
            'low' => 'bg-info',
            default => 'bg-secondary'
        };
    }

    /**
     * Get priority text
     */
    public function getPriorityTextAttribute()
    {
        return match($this->priority) {
            'high' => 'Urgent',
            'medium' => 'Warning',
            'low' => 'Info',
            default => 'Normal'
        };
    }

    /**
     * Get type badge class
     */
    public function getTypeBadgeClassAttribute()
    {
        return match($this->type) {
            'token_tax_expiry' => 'bg-primary',
            'dip_chart_expiry' => 'bg-warning',
            'tracker_expiry' => 'bg-info',
            default => 'bg-secondary'
        };
    }

    /**
     * Get type text
     */
    public function getTypeTextAttribute()
    {
        return match($this->type) {
            'token_tax_expiry' => 'Token Tax',
            'dip_chart_expiry' => 'Dip Chart',
            'tracker_expiry' => 'Tracker',
            default => 'Unknown'
        };
    }
}
