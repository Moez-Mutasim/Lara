<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $primaryKey = 'branch_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'location',
        'manager',
        'phone',
        'is_active',
    ];

    
    public function scopeActive($query)
    {return $query->where('is_active', true);}

    
    public function users()
    {return $this->hasMany(User::class, 'branch_id', 'branch_id');}
}
