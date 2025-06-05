<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'source',
        'source_id',
        'title',
        'description',
        'price',
        'surface',
        'rooms',
        'bedrooms',
        'city_id',
        'latitude',
        'longitude',
        'property_type',
        'transaction_type',
        'url',
        'images',
        'additional_info',
        'is_active',
        'scraped_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'surface' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'images' => 'array',
        'additional_info' => 'array',
        'is_active' => 'boolean',
        'scraped_at' => 'datetime',
    ];
    
    /**
     * Relation avec la ville
     */
    public function cityModel()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
