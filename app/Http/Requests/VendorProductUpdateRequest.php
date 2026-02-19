<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorProductUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title' => ['sometimes','required','string','max:255'],
            'category_id' => ['sometimes','required','integer','exists:categories,id'],
            'short_description' => ['nullable','string','max:1000'],
            'description' => ['nullable','string','max:20000'],
            'location' => ['nullable','string','max:255'],
            'currency' => ['nullable','string','max:5'],
            'price' => ['nullable','numeric','min:0'],
            'old_price' => ['nullable','numeric','min:0'],
            'moq' => ['nullable','integer','min:1'],
            'is_deal' => ['nullable','boolean'],
            'ideal_for' => ['nullable','array'],
            'ideal_for.*' => ['string','max:80'],
            'tags' => ['nullable','array'],
            'tags.*' => ['string','max:40'],
            'is_active' => ['nullable','boolean'],
        ];
    }
}
