<?php

namespace App\Utils;

use Exception;

class FileUploaderUtils
{
    public function upload(object $file, string $filePath = 'uploads/'): string
    {
        if (file_exists($filePath)) {
            try {
                mkdir($filePath);
            } catch (Exception $e) {
            }
        }

        $fullNameExtension = trim($file->getClientOriginalName());
        $arr = preg_split('/\./', $fullNameExtension);
        $extension = array_pop($arr);
        $fullName = implode('.', $arr);
        $fileName = $fullName . __ . uniqid() . __ . '.' . $extension;
        $file->move($filePath, $fileName);
        \Log::debug('File Uploaded');

        return $fileName;
    }

    public function uploadMultiple(?object $files, string $filesPath = 'uploads/'): ?string
    {
        if (isset($files)) {
            if (file_exists($filesPath)) {
                try {
                    mkdir($filesPath);
                } catch (Exception $e) {
                }
            }
            $filesNameArr = [];
            foreach ($files as $key => $file) {
                $fullNameExtension = trim($file->getClientOriginalName());
                $arr = preg_split('/\./', $fullNameExtension);
                $extension = array_pop($arr);
                $fullName = implode('.', $arr);
                $fileName = $fullName . __ . uniqid() . __ . '.' . $extension;
                $file->move($filesPath, $fileName);
                $filesNameArr[$key] = $fileName;
            }

            return json_encode($filesNameArr);
        }

        return '';
    }
}
