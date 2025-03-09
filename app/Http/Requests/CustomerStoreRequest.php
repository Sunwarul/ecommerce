<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'phone' => 'required',
            'email' => 'nullable|email',
            'address' => 'required|string',
            'city' => 'nullable',
            'country' => 'nullable',
            'zip_code' => 'nullable',
            'land_mark' => 'nullable',
            'is_active' => 'nullable|boolean',
        ];
    }
}
