<?php
namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class TicketCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public SupportTicket $ticket) {}

    public function via(object $notifiable): array
    {
        return ['database', 'webpush'];
    }

    public function toWebPush(object $notifiable, mixed $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('🎫 New Ticket — ' . $this->ticket->subject)
            ->body('Customer: ' . $this->ticket->user->name . ' · Priority: ' . ucfirst($this->ticket->priority))
            ->action('View Ticket', url('/admin/tickets/' . $this->ticket->id))
            ->icon('/images/logo.png');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ticket_created',
            'ticket_id' => $this->ticket->id,
            'subject' => $this->ticket->subject,
            'priority' => $this->ticket->priority,
            'customer_name' => $this->ticket->user->name,
            'message' => 'New ticket "' . $this->ticket->subject . '" created by ' . $this->ticket->user->name,
        ];
    }
}
