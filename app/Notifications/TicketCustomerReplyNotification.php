<?php
namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class TicketCustomerReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public SupportTicket $ticket, public string $replyMessage) {}

    public function via(object $notifiable): array
    {
        return ['database', 'webpush'];
    }

    public function toWebPush(object $notifiable, mixed $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('💬 Ticket Reply — ' . $this->ticket->subject)
            ->body('Customer replied: ' . substr($this->replyMessage, 0, 50) . '...')
            ->action('View Ticket', url('/admin/tickets/' . $this->ticket->id))
            ->icon('/images/logo.png');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ticket_customer_reply',
            'ticket_id' => $this->ticket->id,
            'subject' => $this->ticket->subject,
            'customer_name' => $this->ticket->user->name,
            'message' => $this->replyMessage,
            'full_message' => 'Customer replied to ticket "' . $this->ticket->subject . '"',
        ];
    }
}
