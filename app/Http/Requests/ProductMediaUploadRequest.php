<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductMediaUploadRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // images[] min 3 max 10 (only applied if images present in this request)
            'images' => ['nullable','array','min:3','max:10'],
            'images.*' => ['image','mimes:jpg,jpeg,png,webp','max:5120'],

            // videos[] optional
            'videos' => ['nullable','array','max:5'],
            'videos.*' => ['file','mimetypes:video/mp4,video/quicktime,video/x-matroska','max:51200'], // 50MB
        ];
    }
}
