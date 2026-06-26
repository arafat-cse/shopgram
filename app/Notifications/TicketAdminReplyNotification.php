<?php
namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class TicketAdminReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public SupportTicket $ticket, public string $replyMessage) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'webpush'];
    }

    public function toWebPush(object $notifiable, mixed $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('💬 Ticket Reply — ' . $this->ticket->subject)
            ->body('Admin has replied to your ticket')
            ->action('View Ticket', url('/customer/tickets/' . $this->ticket->id))
            ->icon('/images/logo.png');
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ticket Reply — ' . $this->ticket->subject)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Admin has replied to your ticket: **' . $this->ticket->subject . '**')
            ->line('**Reply:** ' . $this->replyMessage)
            ->action('View Ticket', url('/customer/tickets/' . $this->ticket->id))
            ->line('Thank you for your patience!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'ticket_admin_reply',
            'ticket_id' => $this->ticket->id,
            'subject' => $this->ticket->subject,
            'message' => $this->replyMessage,
            'full_message' => 'Admin replied to your ticket "' . $this->ticket->subject . '"',
        ];
    }
}
