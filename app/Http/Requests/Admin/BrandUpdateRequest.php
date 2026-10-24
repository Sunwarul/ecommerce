<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\IgnoresStoredFilePaths;
use App\Rules\FileUploadRule;
use Illuminate\Foundation\Http\FormRequest;

class BrandUpdateRequest extends FormRequest
{
    use IgnoresStoredFilePaths;

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
            'name' => 'required|string|max:255|unique:brands,name,'.$this->route('brand'),
            'description' => 'nullable|string',
            'photo' => $this->hasFile('photo') ? ['nullable', new FileUploadRule] : 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }
}
