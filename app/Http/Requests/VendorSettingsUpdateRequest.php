<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorSettingsUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'new_order_received' => ['sometimes','boolean'],
            'order_status_updates' => ['sometimes','boolean'],
            'order_cancelled' => ['sometimes','boolean'],
            'new_customer_message' => ['sometimes','boolean'],
            'admin_messages' => ['sometimes','boolean'],
        ];
    }
}
