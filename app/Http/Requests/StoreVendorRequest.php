<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'  => ['required','string','max:120'],
            'email' => ['nullable','email','max:255','unique:vendors,email'],
            'phone' => ['nullable','string','max:30'],
        ];
    }
}
