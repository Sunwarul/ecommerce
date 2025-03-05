<?php

namespace Modules\Product\Http\Requests;

use Modules\Product\Rules\FileUploadRule;
use Illuminate\Foundation\Http\FormRequest;

class BrandUpdateRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:categories,name,' . $this->id,
            'description' => 'nullable|string',
            'photo' => $this->hasFile('photo') ? ['nullable', new FileUploadRule()] : 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }
}
