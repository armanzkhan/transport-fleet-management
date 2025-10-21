<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shortcut extends Model
{
    protected $fillable = [
        'shortcut',
        'full_form',
        'description',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function expandText($text)
    {
        $shortcuts = self::where('is_active', true)->get();
        
        foreach ($shortcuts as $shortcut) {
            $text = str_ireplace($shortcut->shortcut, $shortcut->full_form, $text);
        }
        
        return $text;
    }
}
