<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'phone'  => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ];
    }
}
