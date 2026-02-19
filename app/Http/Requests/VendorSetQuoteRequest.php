<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorSetQuoteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'quoted_price' => ['required','numeric','min:0'],
            'currency' => ['nullable','string','max:5'],
            'quoted_moq' => ['nullable','integer','min:1'],
            'note' => ['nullable','string','max:2000'],
        ];
    }
}
