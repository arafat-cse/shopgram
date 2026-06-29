<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveChatMessage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'chat_id', 'sender_type', 'sender_name', 'user_id',
        'message', 'attachment', 'attachment_type', 'attachment_name',
        'attachment_size', 'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'attachment_size' => 'integer',
        'created_at' => 'datetime',
    ];

    public function chat()
    {
        return $this->belongsTo(LiveChat::class);
    }
}
