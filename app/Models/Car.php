<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Car extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'car_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'model',
        'brand',
        'rental_price',
        'availability',
        'features',
        'image'
    ];

    protected $casts = [
        'features' => 'array',
        'availability' => 'boolean',
        'rental_price' => 'decimal:2',
    ];



    public function scopeAvailable($query)
    {
        return $query->where('availability', true);
    }

    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', $brand);
    }


    public function getFormattedRentalPriceAttribute()
    {
        return '$' . number_format($this->rental_price, 2);
    }
}
