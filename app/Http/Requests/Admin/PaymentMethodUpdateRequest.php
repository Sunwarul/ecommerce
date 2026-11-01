<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\IgnoresStoredFilePaths;
use App\Rules\FileUploadRule;
use Illuminate\Foundation\Http\FormRequest;

class PaymentMethodUpdateRequest extends FormRequest
{
    use IgnoresStoredFilePaths;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'is_active' => ['required'],
            'photo' => $this->hasFile('photo') ? ['nullable', new FileUploadRule] : 'nullable|string',
        ];
    }
}
