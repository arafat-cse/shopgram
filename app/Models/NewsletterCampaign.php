<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterCampaign extends Model
{
    protected $fillable = [
        'created_by',
        'from_name',
        'from_email',
        'subject',
        'preview_text',
        'image_path',
        'body',
        'status',
        'recipient_count',
        'processed_count',
        'sent_count',
        'failed_count',
        'queued_at',
        'sent_at',
    ];

    protected $casts = [
        'queued_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function markProcessed(bool $sent): void
    {
        $this->increment('processed_count');

        if ($sent) {
            $this->increment('sent_count');
        } else {
            $this->increment('failed_count');
        }

        $this->refresh();

        if ($this->processed_count >= $this->recipient_count && $this->status !== 'sent') {
            $this->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }
    }
}
