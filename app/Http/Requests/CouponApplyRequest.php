<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponApplyRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'coupon_code' => 'required|string|max:100|exists:coupons,code',
        ];
    }

    public function messages(): array
    {
        return [
            'coupon_code.exists' => 'Invalid coupon code.',
        ];
    }
}
