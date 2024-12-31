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
        'image'
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'price' => 'decimal:2',
    ];


    public function scopeAvailable($query){return $query->where('seats_available', '>', 0);}
    public function scopeByClass($query, $class){return $query->where('class', $class);}
    public function scopeByRoute($query, $departure, $destination){return $query->where('departure', $departure)->where('destination', $destination);}


    public function getFormattedPriceAttribute(){return '$' . number_format($this->price, 2);}
    public function setDurationAttribute($value){$this->attributes['duration'] = $value . ' hours';}


    public function bookings(){return $this->hasMany(Booking::class, 'flight_id', 'flight_id');}


    public function isFullyBooked(){return $this->seats_available <= 0;}
    public function bookSeat()
    {
        if ($this->isFullyBooked()) {
            throw new \Exception('No seats available for this flight.');}
        $this->decrement('seats_available');
    }
}
