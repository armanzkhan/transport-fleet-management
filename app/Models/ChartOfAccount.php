<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChartOfAccount extends Model
{
    protected $fillable = [
        'account_code',
        'account_name',
        'account_name_urdu',
        'account_type',
        'parent_id',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function cashBooks(): HasMany
    {
        return $this->hasMany(CashBook::class);
    }

    public function getAccountCodeAttribute()
    {
        if (!$this->attributes['account_code']) {
            $this->attributes['account_code'] = 'ACC-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
            $this->save();
        }
        return $this->attributes['account_code'];
    }

    public function getFullPathAttribute()
    {
        $path = $this->account_name;
        $parent = $this->parent;
        
        while ($parent) {
            $path = $parent->account_name . ' > ' . $path;
            $parent = $parent->parent;
        }
        
        return $path;
    }

    public function getAccountTree()
    {
        return $this->children()->with('getAccountTree')->get();
    }
}
