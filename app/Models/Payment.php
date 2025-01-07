<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'payment_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'booking_id',
        'payment_reference',
        'amount',
        'payment_method',
        'payment_status',
        'transaction_fee',
        'currency',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_fee' => 'decimal:2',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function booking(){return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');}


    public function scopeCompleted($query){return $query->where('payment_status', 'completed');}
    public function scopePending($query){return $query->where('payment_status', 'pending');}
    public function scopeFailed($query){return $query->where('payment_status', 'failed');}
    public function scopeRefunded($query){return $query->where('payment_status', 'refunded');}


    public function getFormattedAmountAttribute(){return $this->currency . ' ' . number_format($this->amount, 2);}
    public function setAmountAttribute($value){$this->attributes['amount'] = round($value, 2);}


    public function markAsCompleted(){$this->update(['payment_status' => 'completed', 'paid_at' => now()]);}
    public function markAsFailed(){$this->update(['payment_status' => 'failed']);}
    public function markAsRefunded(){$this->update(['payment_status' => 'refunded']);}
}
