<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorOnboardingStep3Request extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'years_in_business' => ['required','integer','min:0','max:100'],
            'export_markets'    => ['required','array','min:1'],
            'export_markets.*'  => ['string','max:80'],
            'languages'         => ['required','array','min:1'],
            'languages.*'       => ['string','max:50'],
            'image'             => ['required','image','mimes:jpg,jpeg,png,webp','max:4096'],
        ];
    }
}
