<?php
namespace App\Services;

use App\Models\User;

class PendingActionService
{
    public function execute(User $user): string
    {
        $action   = session('pending_action');
        $prodId   = session('pending_product_id');
        $varId    = session('pending_variant_id');
        $qty      = session('pending_quantity', 1);
        $intended = session('intended_url');

        $redirect = '/';

        switch ($action) {
            case 'add_to_cart':
                app(CartService::class)->addItem($user, $prodId, $varId, $qty);
                $redirect = route('cart.index');
                break;

            case 'buy_now':
                app(CartService::class)->addItem($user, $prodId, $varId, $qty);
                $redirect = route('checkout.index');
                break;

            case 'checkout':
                $redirect = route('checkout.index');
                break;

            case 'add_to_wishlist':
                app(WishlistService::class)->add($user, $prodId);
                $redirect = $intended ?? route('products.index');
                break;

            case 'order_tracking':
                $redirect = route('order.tracking');
                break;
        }

        session()->forget([
            'pending_action', 'pending_product_id',
            'pending_variant_id', 'pending_quantity', 'intended_url',
        ]);

        return $redirect;
    }
}
