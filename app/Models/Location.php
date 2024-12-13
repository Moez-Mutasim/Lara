<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';
    protected $primaryKey = 'location_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'type',
        'latitude',
        'longitude',
        'description',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_active' => 'boolean',
    ];

    
    public function scopeActive($query)
    {return $query->where('is_active', true);}

    
    public function scopeByType($query, $type)
    {return $query->where('type', $type);}

    
    public function getFormattedCoordinatesAttribute()
    {return "{$this->latitude}, {$this->longitude}";}

    
    public function departingFlights()
    {return $this->hasMany(Flight::class, 'departure_id', 'location_id');}

    public function arrivingFlights()
    {return $this->hasMany(Flight::class, 'destination_id', 'location_id');}
}
