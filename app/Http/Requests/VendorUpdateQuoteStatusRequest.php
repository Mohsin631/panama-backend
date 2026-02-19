<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorUpdateQuoteStatusRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'status' => ['required','in:negotiating,quoted,accepted,paid,shipped,closed,cancelled'],
            'note' => ['nullable','string','max:2000'],
        ];
    }
}
