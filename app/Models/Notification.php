<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'notification_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'message',
        'is_read',
        'type',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function scopeUnread($query){return $query->where('is_read', false);}
    public function scopeRead($query){return $query->where('is_read', true);}
    public function scopeByType($query, $type){return $query->where('type', $type);}


    public function getFormattedMessageAttribute(){return ucfirst($this->message);}


    public function user(){return $this->belongsTo(User::class, 'user_id', 'id');}


    public function markAsRead(){$this->update(['is_read' => true]);}
    public function markAsUnread(){$this->update(['is_read' => false]);}
}
