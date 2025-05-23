<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseUpdateRequest extends FormRequest
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
            'name' => 'required|string'.$this->id,
            'code' => 'required',
            'address' => 'nullable',
            'description' => 'nullable',
            'under_business' => 'nullable',
            'is_active' => 'nullable|boolean',
        ];
    }
}
