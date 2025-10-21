<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterData extends Model
{
    protected $fillable = [
        'type',
        'name',
        'name_urdu',
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

    public static function getByType($type)
    {
        return self::where('type', $type)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public static function getLoadingPoints()
    {
        return self::getByType('loading_point');
    }

    public static function getDestinations()
    {
        return self::getByType('destination');
    }

    public static function getProducts()
    {
        return self::getByType('product');
    }

    public static function getCompanies()
    {
        return self::getByType('company');
    }

    public static function getCarriageNames()
    {
        return self::getByType('carriage_name');
    }

    public static function getPumpNames()
    {
        return self::getByType('pump_name');
    }
}
