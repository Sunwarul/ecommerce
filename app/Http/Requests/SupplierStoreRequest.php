<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierStoreRequest extends FormRequest
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
            'secur_number' => 'required',
            'nid_number' => 'required|integer',
            'email' => 'nullable|email',
            'address' => 'required',
            'city' => 'nullable',
            'country' => 'nullable',
            'zip_code' => 'nullable',
            'group' => 'nullable',
            'land_mark' => 'nullable',
            'is_active' => 'nullable|boolean',
        ];
    }
}
