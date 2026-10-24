<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Http\UploadedFile;

/**
 * Edit forms echo back the stored path of an existing photo/file (a string). Drop anything that
 * isn't a fresh upload so validation passes and the controller keeps the current file.
 */
trait IgnoresStoredFilePaths
{
    protected array $fileFields = ['photo', 'file', 'thumbnail'];

    protected function prepareForValidation(): void
    {
        foreach ($this->fileFields as $field) {
            if ($this->exists($field) && ! $this->file($field) instanceof UploadedFile) {
                $this->request->remove($field);
                $this->query->remove($field);
            }
        }
    }
}
