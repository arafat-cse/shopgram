<?php
namespace App\Services;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\CartItem;

class CartService
{
    public function addItem(User $user, int $productId, ?int $variantId, int $qty = 1): CartItem
    {
        $product = Product::findOrFail($productId);

        $price = $product->sale_price ?? $product->regular_price;

        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            if ($variant && $variant->price) {
                $price = $variant->price;
            }
        }

        $existing = CartItem::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->first();

        if ($existing) {
            $existing->increment('quantity', $qty);
            $existing->price = $price;
            $existing->save();
            return $existing;
        }

        return CartItem::create([
            'user_id'    => $user->id,
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity'   => $qty,
            'price'      => $price,
        ]);
    }

    public function getItems(User $user)
    {
        return CartItem::with(['product', 'variant'])
            ->where('user_id', $user->id)
            ->get();
    }

    public function getCount(User $user): int
    {
        return CartItem::where('user_id', $user->id)->sum('quantity');
    }

    public function getSubtotal(User $user): float
    {
        return CartItem::where('user_id', $user->id)
            ->get()
            ->sum(fn($item) => $item->price * $item->quantity);
    }

    public function updateQuantity(User $user, int $cartItemId, int $qty): void
    {
        CartItem::where('id', $cartItemId)
            ->where('user_id', $user->id)
            ->update(['quantity' => max(1, $qty)]);
    }

    public function removeItem(User $user, int $cartItemId): void
    {
        CartItem::where('id', $cartItemId)
            ->where('user_id', $user->id)
            ->delete();
    }

    public function clear(User $user): void
    {
        CartItem::where('user_id', $user->id)->delete();
    }
}
