<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveChat extends Model
{
    protected $fillable = [
        'session_id', 'guest_name', 'guest_phone',
        'user_id', 'assigned_to', 'status', 'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function messages()
    {
        return $this->hasMany(LiveChatMessage::class, 'chat_id')->orderBy('created_at');
    }

    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(LiveChatMessage::class, 'chat_id')->latestOfMany('created_at');
    }
}
