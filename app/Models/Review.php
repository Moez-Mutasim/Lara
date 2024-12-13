<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'review_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'flight_id',
        'car_id',
        'hotel_id',
        'rating',
        'comment',
    ];

    protected $casts = ['rating' => 'decimal:1',];


    public function user(){return $this->belongsTo(User::class, 'user_id');}
    public function flight(){return $this->belongsTo(Flight::class, 'flight_id');}
    public function car(){return $this->belongsTo(Car::class, 'car_id');}
    public function hotel(){return $this->belongsTo(Hotel::class, 'hotel_id');}


    public function scopeByRating($query, $rating){return $query->where('rating', '>=', $rating);}
    public function scopeByEntity($query, $entityType, $entityId){return $query->where("{$entityType}_id", $entityId);}


    public function getFormattedRatingAttribute(){return number_format($this->rating, 1) . ' / 5';}
    public function getFormattedCommentAttribute(){return ucfirst($this->comment);}


    public function isPositive(){return $this->rating >= 4.0;}
    public function isNegative(){return $this->rating <= 2.0;}
}
