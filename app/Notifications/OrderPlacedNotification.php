<?php
namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class OrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'webpush'];
    }

    public function toWebPush(object $notifiable, mixed $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('🛒 New Order — ' . $this->order->order_number)
            ->body('৳' . number_format($this->order->total, 0) . ' · ' . strtoupper($this->order->payment_method))
            ->action('View Order', url('/admin/orders/' . $this->order->id))
            ->icon('/images/logo.png');
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Order Confirmed — ' . $this->order->order_number)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your order **' . $this->order->order_number . '** has been placed successfully.')
            ->line('**Total:** ৳' . number_format($this->order->total, 2))
            ->line('**Payment Method:** ' . strtoupper($this->order->payment_method))
            ->action('View Order', url('/customer/orders/' . $this->order->id))
            ->line('Thank you for shopping with ShopGram!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'order_placed',
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
            'total'        => $this->order->total,
            'message'      => 'Order ' . $this->order->order_number . ' placed successfully.',
        ];
    }
}
