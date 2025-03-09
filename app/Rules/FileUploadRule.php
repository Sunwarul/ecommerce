<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FileUploadRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
        $maxFileSize = 10 * 1024 * 1024; // 10 MB in bytes

        if (!$value->isValid()) {
            $fail('The file upload failed.');
            return;
        }

        $extension = strtolower($value->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions)) {
            $fail('The file type is not allowed.');
            return;
        }

        if ($value->getSize() > $maxFileSize) {
            $fail('The file size exceeds the maximum limit of 10 MB.');
            return;
        }
    }
}
