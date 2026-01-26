<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorRequest extends FormRequest
{
    public function rules(): array
    {
        $vendorId = $this->route('vendor')?->id;

        return [
            'name'  => ['sometimes','required','string','max:120'],
            'email' => ['nullable','email','max:255',"unique:vendors,email,{$vendorId}"],
            'phone' => ['nullable','string','max:30'],
        ];
    }
}
