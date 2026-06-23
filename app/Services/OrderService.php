<?php
namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private CartService   $cartService,
        private CouponService $couponService,
        private InventoryService $inventoryService
    ) {}

    public function placeOrder(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            $cartItems = CartItem::with(['product', 'variant'])
                ->where('user_id', $user->id)
                ->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Cart is empty.');
            }

            // Validate stock
            foreach ($cartItems as $item) {
                $stockQty = $item->variant
                    ? $item->variant->stock_quantity
                    : $item->product->stock_quantity;

                if ($stockQty < $item->quantity) {
                    throw new \Exception("{$item->product->name} is out of stock.");
                }
            }

            $subtotal = $cartItems->sum(fn($i) => $i->price * $i->quantity);
            $discount = 0;
            $coupon   = null;

            if (!empty($data['coupon_id'])) {
                $coupon   = \App\Models\Coupon::find($data['coupon_id']);
                $discount = $coupon ? $coupon->calculateDiscount($subtotal) : 0;
            }

            $shippingCharge = $data['shipping_charge'] ?? 0;
            $taxAmount      = 0;
            $total          = $subtotal - $discount + $shippingCharge + $taxAmount;

            $order = Order::create([
                'order_number'    => Order::generateOrderNumber(),
                'user_id'         => $user->id,
                'coupon_id'       => $coupon?->id,
                'shipping_zone_id'=> $data['shipping_zone_id'] ?? null,
                'billing_address' => $data['billing_address'],
                'shipping_address'=> $data['shipping_address'],
                'subtotal'        => $subtotal,
                'discount_amount' => $discount,
                'shipping_charge' => $shippingCharge,
                'tax_amount'      => $taxAmount,
                'total'           => $total,
                'payment_method'  => $data['payment_method'],
                'payment_status'  => 'unpaid',
                'delivery_method' => $data['delivery_method'] ?? null,
                'order_note'      => $data['order_note'] ?? null,
                'status'          => 'pending',
                'placed_at'       => now(),
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item->product_id,
                    'variant_id'   => $item->variant_id,
                    'product_name' => $item->product->name,
                    'variant_info' => $item->variant ? [
                        'size'   => $item->variant->size,
                        'color'  => $item->variant->color,
                        'weight' => $item->variant->weight,
                    ] : null,
                    'quantity'   => $item->quantity,
                    'unit_price' => $item->price,
                    'total_price'=> $item->price * $item->quantity,
                ]);

                // Deduct stock
                $this->inventoryService->deductStock(
                    $item->product_id,
                    $item->variant_id,
                    $item->quantity,
                    "Order #{$order->order_number}",
                    $user->id
                );
            }

            // Create payment record
            Payment::create([
                'order_id'   => $order->id,
                'method'     => $data['payment_method'],
                'status'     => 'pending',
                'amount'     => $total,
                'screenshot' => $data['payment_screenshot'] ?? null,
            ]);

            // Record coupon usage
            if ($coupon) {
                $this->couponService->recordUsage($coupon, $user, $order->id);
            }

            // Initial status history
            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => 'pending',
                'note'       => 'Order placed by customer.',
                'updated_by' => $user->id,
            ]);

            // Clear cart
            $this->cartService->clear($user);

            return $order;
        });
    }

    public function updateStatus(Order $order, string $status, string $note = '', int $adminId = null): void
    {
        $order->update(['status' => $status]);

        OrderStatusHistory::create([
            'order_id'   => $order->id,
            'status'     => $status,
            'note'       => $note,
            'updated_by' => $adminId,
        ]);
    }
}
