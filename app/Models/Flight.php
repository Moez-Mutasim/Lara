<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;

    protected $primaryKey = 'flight_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'airline_name',
        'departure',
        'destination',
        'departure_time',
        'arrival_time',
        'duration',
        'price',
        'seats_available',
        'class',
        'is_available',
        'image',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];



    // Relationships
    public function departure(){return $this->belongsTo(Location::class, 'departure_id', 'location_id');}
    public function destination(){return $this->belongsTo(Location::class, 'destination_id', 'location_id');}


    
    // Scopes
    public function scopeAvailable($query){return $query->where('is_available', true)->where('seats_available', '>', 0);}


    // Aditions
    public function getFormattedPriceAttribute(){return '$' . number_format($this->price, 2);}
    public function isFullyBooked(){return $this->seats_available <= 0;}
    public function bookSeat()
    {
        if ($this->isFullyBooked()) {
            throw new \Exception('No seats available for this flight.');
        }
        $this->decrement('seats_available');
    }
}
