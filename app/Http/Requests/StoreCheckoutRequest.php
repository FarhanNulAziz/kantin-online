<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCheckoutRequest extends FormRequest
{
    /**
     * Pengguna sudah melewati middleware auth.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Hanya metode pembayaran yang diterima dari pengguna.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_method' => ['required', Rule::in(['cod', 'online'])],
        ];
    }
}
