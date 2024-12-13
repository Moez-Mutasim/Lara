<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'country_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'code',
        'iso_alpha_3',
        'continent',
        'currency',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean',];

    
    public function getFormattedNameAttribute()
    {return ucfirst($this->name);}

    public function setNameAttribute($value)
    {$this->attributes['name'] = strtolower($value);}

    
    public function users()
    {return $this->hasMany(User::class, 'country_id', 'country_id');}

    public function bookings()
    {return $this->hasMany(Booking::class, 'country_id', 'country_id');}

   
    public function scopeActive($query)
    {return $query->where('is_active', true);}

    public function scopeByContinent($query, $continent)
    {return $query->where('continent', $continent);}

    
    public function isActive()
    {return $this->is_active;}
}
