<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'billing_name'     => 'required|string|max:255',
            'billing_phone'    => 'required|string|max:20',
            'billing_address'  => 'required|string',
            'billing_city'     => 'required|string|max:100',
            'billing_district' => 'required|string|max:100',
            'billing_zip'      => 'nullable|string|max:20',
            'same_as_billing'  => 'boolean',
            'shipping_name'    => 'required_without:same_as_billing|string|max:255',
            'shipping_phone'   => 'required_without:same_as_billing|string|max:20',
            'shipping_address' => 'required_without:same_as_billing|string',
            'shipping_city'    => 'required_without:same_as_billing|string|max:100',
            'shipping_district'=> 'required_without:same_as_billing|string|max:100',
            'payment_method'   => 'required|in:cod,bank,mobile',
            'delivery_method'  => 'nullable|string',
            'order_note'       => 'nullable|string|max:1000',
            'shipping_zone_id' => 'nullable|exists:shipping_zones,id',
            'payment_screenshot'=> 'nullable|image|max:2048',
        ];
    }
}
