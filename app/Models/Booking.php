<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'booking_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'flight_id',
        'hotel_id',
        'car_id',
        'booking_date',
        'total_price',
        'status',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'booking_date' => 'date',
        'status' => 'string',
    ];


    public function user(){return $this->belongsTo(User::class);}
    public function flight(){return $this->belongsTo(Flight::class);}
    public function hotel(){return $this->belongsTo(Hotel::class);}
    public function car(){return $this->belongsTo(Car::class);}


    public function scopePending($query){return $query->where('status', 'pending');}
    public function scopeCompleted($query){return $query->where('status', 'completed');}
    public function scopeCanceled($query){return $query->where('status', 'canceled');}

    

    public function getFormattedTotalPriceAttribute(){return '$' . number_format($this->total_price, 2);}
    public function setBookingDateAttribute($value){
    if ($value instanceof \DateTime) {
        $this->attributes['booking_date'] = $value->format('Y-m-d');
    } elseif (is_string($value)) {
        $this->attributes['booking_date'] = (new \DateTime($value))->format('Y-m-d');
    } else {
        throw new \InvalidArgumentException('Invalid booking date format.');
    }}
    public function getBookingDateAttribute($value){return new \DateTime($value);}
    


    public function isPending(){return $this->status === 'pending';}
    public function markAsCompleted(){$this->update(['status' => 'completed']);}
}
