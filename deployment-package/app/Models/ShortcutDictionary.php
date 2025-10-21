<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShortcutDictionary extends Model
{
    use HasFactory;

    protected $fillable = [
        'shortcut',
        'full_form',
        'description',
        'category',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the user who created the shortcut
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for active shortcuts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get all shortcuts as array for JavaScript
     */
    public static function getShortcutsArray()
    {
        return self::active()
            ->get()
            ->mapWithKeys(function($shortcut) {
                return [$shortcut->shortcut => $shortcut->full_form];
            })
            ->toArray();
    }

    /**
     * Get shortcuts by category
     */
    public static function getShortcutsByCategory($category = null)
    {
        $query = self::active();
        
        if ($category) {
            $query->byCategory($category);
        }
        
        return $query->get();
    }

    /**
     * Get available categories
     */
    public static function getCategories()
    {
        return self::active()
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();
    }
}
