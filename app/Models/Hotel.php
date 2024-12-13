<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'hotel_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'city',
        'price_per_night',
        'rating',
        'amenities',
        'availability',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'price_per_night' => 'decimal:2',
        'amenities' => 'array',
        'availability' => 'boolean',
    ];


    public function scopeAvailable($query){return $query->where('availability', true);}
    public function scopeByCity($query, $city){return $query->where('city', $city);}
    public function scopeByRating($query, $minRating){return $query->where('rating', '>=', $minRating);}


    public function getFormattedPriceAttribute(){return '$' . number_format($this->price_per_night, 2);}
    public function getFormattedRatingAttribute(){return number_format($this->rating, 1) . ' / 5';}


    public function bookings(){return $this->hasMany(Booking::class, 'hotel_id', 'hotel_id');}


    public function markAsUnavailable(){$this->update(['availability' => false]);}
    public function markAsAvailable(){$this->update(['availability' => true]);}
}
