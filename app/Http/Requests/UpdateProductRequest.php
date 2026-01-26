<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'vendor_id'   => ['sometimes','required','integer','exists:vendors,id'],
            'name'        => ['sometimes','required','string','max:140'],
            'description' => ['nullable','string'],
            'price'       => ['sometimes','required','numeric','min:0'],
            'stock'       => ['sometimes','required','integer','min:0'],
        ];
    }
}
