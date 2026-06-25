<?php
namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockHistory;
use App\Models\User;
use App\Notifications\LowStockAlertNotification;

class InventoryService
{
    public function stockIn(int $productId, ?int $variantId, int $qty, string $note = '', int $userId = null): void
    {
        if ($variantId) {
            ProductVariant::where('id', $variantId)->increment('stock_quantity', $qty);
        } else {
            Product::where('id', $productId)->increment('stock_quantity', $qty);
        }

        StockHistory::create([
            'product_id' => $productId,
            'variant_id' => $variantId,
            'type'       => 'stock_in',
            'quantity'   => $qty,
            'note'       => $note,
            'created_by' => $userId,
        ]);
    }

    public function stockOut(int $productId, ?int $variantId, int $qty, string $note = '', int $userId = null): void
    {
        if ($variantId) {
            ProductVariant::where('id', $variantId)->decrement('stock_quantity', $qty);
        } else {
            Product::where('id', $productId)->decrement('stock_quantity', $qty);
        }

        StockHistory::create([
            'product_id' => $productId,
            'variant_id' => $variantId,
            'type'       => 'stock_out',
            'quantity'   => $qty,
            'note'       => $note,
            'created_by' => $userId,
        ]);

        $this->checkLowStock($productId);
    }

    private function checkLowStock(int $productId): void
    {
        $product = Product::find($productId);
        if (!$product || $product->low_stock_threshold <= 0) return;
        if ($product->stock_quantity > $product->low_stock_threshold) return;
        if ($product->stock_quantity <= 0) return;

        User::role(['Super Admin', 'Admin'])->each(
            fn(User $admin) => $admin->notify(new LowStockAlertNotification($product))
        );
    }

    public function adjust(int $productId, ?int $variantId, int $newQty, string $note = '', int $userId = null): void
    {
        if ($variantId) {
            $current = ProductVariant::find($variantId)?->stock_quantity ?? 0;
            ProductVariant::where('id', $variantId)->update(['stock_quantity' => $newQty]);
        } else {
            $current = Product::find($productId)?->stock_quantity ?? 0;
            Product::where('id', $productId)->update(['stock_quantity' => $newQty]);
        }

        StockHistory::create([
            'product_id' => $productId,
            'variant_id' => $variantId,
            'type'       => 'adjustment',
            'quantity'   => $newQty - $current,
            'note'       => $note ?: "Adjusted from {$current} to {$newQty}",
            'created_by' => $userId,
        ]);
    }

    public function deductStock(int $productId, ?int $variantId, int $qty, string $note = '', int $userId = null): void
    {
        $this->stockOut($productId, $variantId, $qty, $note, $userId);
    }
}
