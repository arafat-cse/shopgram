<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = ['user_id', 'subject', 'status', 'priority'];

    public function user() { return $this->belongsTo(User::class); }
    public function replies() { return $this->hasMany(TicketReply::class, 'ticket_id'); }

    public function scopeOpen($query) { return $query->where('status', 'open'); }
}
