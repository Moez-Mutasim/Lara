<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    use HasFactory;

    protected $primaryKey = 'search_id'; 

    protected $fillable = [
        'user_id', 
        'search_type', 
        'search_details',
    ];

    protected $casts = [
        'search_details' => 'array',
    ];
}
