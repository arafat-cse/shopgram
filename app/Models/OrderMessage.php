<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderMessage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'user_id',
        'sender_role',
        'message',
        'is_read',
        'attachment',
        'attachment_type',
        'attachment_name',
        'attachment_size',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'attachment_size' => 'integer',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
