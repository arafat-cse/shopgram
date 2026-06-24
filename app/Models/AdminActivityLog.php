<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id',
        'description', 'meta', 'ip_address', 'created_at',
    ];

    protected $casts = [
        'meta'       => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'created'         => 'success',
            'updated'         => 'primary',
            'deleted'         => 'danger',
            'status_changed'  => 'warning',
            'blocked'         => 'danger',
            'unblocked'       => 'success',
            'approved'        => 'success',
            'rejected'        => 'danger',
            default           => 'secondary',
        };
    }

    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'created'         => 'bi-plus-circle',
            'updated'         => 'bi-pencil',
            'deleted'         => 'bi-trash',
            'status_changed'  => 'bi-arrow-repeat',
            'blocked'         => 'bi-slash-circle',
            'unblocked'       => 'bi-check-circle',
            'approved'        => 'bi-check-circle',
            'rejected'        => 'bi-x-circle',
            default           => 'bi-activity',
        };
    }
}
