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
        'type',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];



    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query){return $query->where('is_read', false);}
    public function scopeByType($query, $type){return $query->where('type', $type);}


    // Additions
    public function getFormattedMessageAttribute(){return ucfirst($this->message);}

    // Methods
    public function markAsRead(){$this->update(['is_read' => true, 'read_at' => now()]);}
    public function markAsUnread(){$this->update(['is_read' => false, 'read_at' => null]);}
}
