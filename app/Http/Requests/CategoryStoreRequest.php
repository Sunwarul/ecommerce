<?php

namespace App\Http\Requests;

use App\Rules\FileUploadRule;
use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreRequest extends FormRequest
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
            'name' => 'required|string|unique:categories,name',
            'photo' => $this->hasFile('photo') ? ['nullable', new FileUploadRule()] : 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }
}
