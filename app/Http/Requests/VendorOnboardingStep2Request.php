<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorOnboardingStep2Request extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'business_name' => ['required','string','max:255'],
            'category_id'   => ['required','integer','exists:categories,id'],
            'location'      => ['required','string','max:255'],
            'whatsapp_no'   => ['required','string','max:30'],
            'about'         => ['required','string','max:5000'],
        ];
    }
}
