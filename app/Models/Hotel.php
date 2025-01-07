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
        'city_id',
        'price_per_night',
        'rating',
        'amenities',
        'availability',
        'rooms_available',
        'image',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'price_per_night' => 'decimal:2',
        'amenities' => 'array',
        'availability' => 'boolean',
    ];


    // Relationships
    public function city(){return $this->belongsTo(Location::class, 'city_id', 'location_id');}
    public function bookings(){return $this->hasMany(Booking::class, 'hotel_id', 'hotel_id');}


    // Scopes
    public function scopeAvailable($query){return $query->where('availability', true)->where('rooms_available', '>', 0);}


    // Additions
    public function getFormattedPriceAttribute(){return '$' . number_format($this->price_per_night, 2);}
    public function getFormattedRatingAttribute(){return number_format($this->rating, 1) . ' / 5';}

    // Methods
    public function markAsUnavailable(){$this->update(['availability' => false]);}
    public function markAsAvailable(){$this->update(['availability' => true]);}
}
