<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateQuoteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'product_id' => ['required','integer','exists:products,id'],
            'quantity' => ['nullable','integer','min:1'],
            'unit' => ['nullable','string','max:30'],
            'shipping_country' => ['nullable','string','max:80'],
            'shipping_city' => ['nullable','string','max:80'],
            'note' => ['required','string','max:5000'],
        ];
    }
}
