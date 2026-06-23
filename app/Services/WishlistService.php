<?php
namespace App\Services;

use App\Models\User;
use App\Models\Wishlist;

class WishlistService
{
    public function add(User $user, int $productId): void
    {
        Wishlist::firstOrCreate([
            'user_id'    => $user->id,
            'product_id' => $productId,
        ]);
    }

    public function remove(User $user, int $productId): void
    {
        Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->delete();
    }

    public function getCount(User $user): int
    {
        return Wishlist::where('user_id', $user->id)->count();
    }
}
