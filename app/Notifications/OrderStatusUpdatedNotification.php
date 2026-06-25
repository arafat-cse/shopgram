<?php
namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class OrderStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order, public string $note = '') {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'webpush'];
    }

    public function toWebPush(object $notifiable, mixed $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('📦 Order ' . $this->order->order_number . ' Updated')
            ->body('Status: ' . $this->order->status_label . ($this->note ? ' · ' . $this->note : ''))
            ->action('Track Order', url('/customer/order-tracking'))
            ->icon('/images/logo.png');
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Order Update — ' . $this->order->order_number)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your order **' . $this->order->order_number . '** status has been updated.')
            ->line('**New Status:** ' . $this->order->status_label);

        if ($this->note) {
            $mail->line('**Note:** ' . $this->note);
        }

        if ($this->order->courier_tracking_number) {
            $mail->line('**Tracking Number:** ' . $this->order->courier_tracking_number);
        }

        return $mail
            ->action('Track Order', url('/customer/order-tracking'))
            ->line('Thank you for shopping with ShopGram!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'order_status_updated',
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
            'status'       => $this->order->status,
            'message'      => 'Order ' . $this->order->order_number . ' is now ' . $this->order->status_label . '.',
        ];
    }
}
