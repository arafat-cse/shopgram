<?php
namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Product $product) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low Stock Alert — ' . $this->product->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('**' . $this->product->name . '** is running low on stock.')
            ->line('**Current Stock:** ' . $this->product->stock_quantity . ' units')
            ->line('**Threshold:** ' . $this->product->low_stock_threshold . ' units')
            ->action('Manage Inventory', url('/admin/inventory'))
            ->line('Please restock to avoid lost sales.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'low_stock',
            'product_id' => $this->product->id,
            'name'       => $this->product->name,
            'stock'      => $this->product->stock_quantity,
            'threshold'  => $this->product->low_stock_threshold,
            'message'    => $this->product->name . ' is low on stock (' . $this->product->stock_quantity . ' left).',
        ];
    }
}
