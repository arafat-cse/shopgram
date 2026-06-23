<?php
namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\User;

class CouponService
{
    public function validate(string $code, User $user, float $subtotal): array
    {
        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Invalid coupon code.'];
        }

        if (!$coupon->isValid()) {
            return ['valid' => false, 'message' => 'This coupon is expired or inactive.'];
        }

        if ($coupon->min_order_amount && $subtotal < $coupon->min_order_amount) {
            return [
                'valid'   => false,
                'message' => "Minimum order amount is ৳" . number_format($coupon->min_order_amount, 2),
            ];
        }

        if ($coupon->per_user_limit) {
            $used = CouponUsage::where('coupon_id', $coupon->id)
                ->where('user_id', $user->id)
                ->count();
            if ($used >= $coupon->per_user_limit) {
                return ['valid' => false, 'message' => 'You have already used this coupon.'];
            }
        }

        $discount = $coupon->calculateDiscount($subtotal);

        return [
            'valid'    => true,
            'coupon'   => $coupon,
            'discount' => $discount,
            'message'  => "Coupon applied! Discount: ৳" . number_format($discount, 2),
        ];
    }

    public function recordUsage(Coupon $coupon, User $user, int $orderId): void
    {
        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id'   => $user->id,
            'order_id'  => $orderId,
            'used_at'   => now(),
        ]);

        $coupon->increment('used_count');
    }
}
