<?php
namespace App\Services;

use App\Models\CoinTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CoinService
{
    public function __construct(private InventoryService $inventoryService) {}

    /**
     * Credit earned coins once an order is delivered. Idempotent — no-ops
     * for coin-redemption orders since those are settled immediately at
     * redeem() time, not on delivery.
     */
    public function settleForOrder(Order $order): void
    {
        if ($order->coins_awarded_at) {
            return;
        }

        $amount = $order->is_coin_redemption ? -$order->coins_used : $order->coins_earned;
        if ($amount === 0) {
            return;
        }

        DB::transaction(function () use ($order, $amount) {
            $user = User::where('id', $order->user_id)->lockForUpdate()->first();

            $balance = $user->coins_balance + $amount;
            $user->update(['coins_balance' => $balance]);

            CoinTransaction::create([
                'user_id'       => $user->id,
                'order_id'      => $order->id,
                'type'          => $order->is_coin_redemption ? 'redeem' : 'earn',
                'amount'        => $amount,
                'balance_after' => $balance,
                'description'   => $order->is_coin_redemption
                    ? "Order #{$order->order_number} delivered — redeemed for {$order->coins_used} coins"
                    : "Order #{$order->order_number} delivered — earned {$order->coins_earned} coins",
            ]);

            $order->update(['coins_awarded_at' => now()]);
        });
    }

    /**
     * Refund/reverse a previously-settled order's coin effect when it is
     * later cancelled/returned/refunded — covers both a redemption order
     * (coins were deducted at redeem time) and a delivered earn order
     * (coins were credited at delivery). No-ops if never settled.
     */
    public function reverseSettlement(Order $order): void
    {
        if (!$order->coins_awarded_at || $order->coins_reversed_at) {
            return;
        }

        $amount = $order->is_coin_redemption ? $order->coins_used : -$order->coins_earned;
        if ($amount === 0) {
            return;
        }

        DB::transaction(function () use ($order, $amount) {
            $user = User::where('id', $order->user_id)->lockForUpdate()->first();

            $balance = $user->coins_balance + $amount;
            $user->update(['coins_balance' => $balance]);

            CoinTransaction::create([
                'user_id'       => $user->id,
                'order_id'      => $order->id,
                'type'          => $order->is_coin_redemption ? 'reversal_redeem' : 'reversal_earn',
                'amount'        => $amount,
                'balance_after' => $balance,
                'description'   => "Order #{$order->order_number} {$order->status} — reversed",
            ]);

            $order->update(['coins_reversed_at' => now()]);
        });
    }

    /**
     * Redeem a coin-redeemable product for the given user. Balance is checked
     * and deducted immediately when the order is placed (not on delivery) —
     * if the order is later cancelled/returned/refunded, reverseSettlement()
     * refunds the coins back.
     */
    public function redeem(User $user, Product $product, array $address): Order
    {
        return DB::transaction(function () use ($user, $product, $address) {
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
            $lockedProduct = Product::where('id', $product->id)->lockForUpdate()->first();

            if (!$lockedProduct->is_coin_redeemable || !$lockedProduct->coin_price) {
                throw new \Exception('This product is not redeemable with coins.');
            }

            if ($lockedUser->coins_balance < $lockedProduct->coin_price) {
                throw new \Exception('Not enough coins to redeem this product.');
            }

            if ($lockedProduct->stock_quantity < 1) {
                throw new \Exception("{$lockedProduct->name} is out of stock.");
            }

            $order = Order::create([
                'order_number'       => Order::generateOrderNumber(),
                'user_id'            => $user->id,
                'billing_address'    => $address,
                'shipping_address'   => $address,
                'subtotal'           => 0,
                'discount_amount'    => 0,
                'shipping_charge'    => 0,
                'tax_amount'         => 0,
                'total'              => 0,
                'payment_method'     => 'coins',
                'payment_status'     => 'paid',
                'status'             => 'pending',
                'placed_at'          => now(),
                'coins_used'         => $lockedProduct->coin_price,
                'is_coin_redemption' => true,
            ]);

            OrderItem::create([
                'order_id'       => $order->id,
                'product_id'     => $lockedProduct->id,
                'product_name'   => $lockedProduct->name,
                'quantity'       => 1,
                'purchase_price' => $lockedProduct->purchase_price ?? 0,
                'selling_price'  => 0,
                'unit_price'     => 0,
                'total_price'    => 0,
            ]);

            $this->inventoryService->deductStock(
                $lockedProduct->id,
                null,
                1,
                "Coin redemption — Order #{$order->order_number}",
                $user->id
            );

            Payment::create([
                'order_id' => $order->id,
                'method'   => 'coins',
                'status'   => 'paid',
                'amount'   => 0,
            ]);

            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => 'pending',
                'note'       => 'Order placed via coin redemption.',
                'updated_by' => $user->id,
            ]);

            $balance = $lockedUser->coins_balance - $lockedProduct->coin_price;
            $lockedUser->update(['coins_balance' => $balance]);

            CoinTransaction::create([
                'user_id'       => $user->id,
                'order_id'      => $order->id,
                'type'          => 'redeem',
                'amount'        => -$lockedProduct->coin_price,
                'balance_after' => $balance,
                'description'   => "Redeemed {$lockedProduct->name} for {$lockedProduct->coin_price} coins",
            ]);

            $order->update(['coins_awarded_at' => now()]);

            return $order;
        });
    }

    /**
     * Manual admin balance correction/bonus — applied immediately.
     */
    public function adjust(User $user, int $amount, string $reason): void
    {
        DB::transaction(function () use ($user, $amount, $reason) {
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

            $balance = $lockedUser->coins_balance + $amount;
            $lockedUser->update(['coins_balance' => $balance]);

            CoinTransaction::create([
                'user_id'       => $lockedUser->id,
                'order_id'      => null,
                'type'          => 'admin_adjust',
                'amount'        => $amount,
                'balance_after' => $balance,
                'description'   => $reason,
            ]);
        });
    }
}
