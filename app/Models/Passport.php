<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Passport extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'passports';
    protected $primaryKey = 'passport_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'passport_number',
        'full_name',
        'country_of_issue',
        'issue_date',
        'expiry_date',
        'passport_image',
        'is_verified',
    ];


    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_verified' => 'boolean',
    ];

    public function user(){return $this->belongsTo(User::class);}


    public function getFormattedExpiryDateAttribute(){return $this->expiry_date->format('F j, Y');}
    public function getFormattedIssueDateAttribute(){return $this->issue_date->format('F j, Y');}


    public function markAsVerified(){$this->update(['is_verified' => true]);}
    public function isExpired(){return $this->expiry_date->isPast();}


}
