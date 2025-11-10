<?php

use App\Models\Setting;

if (!function_exists('renderTemplate')) {
    function renderTemplate($templateBody, array $data): string
    {
        foreach ($data as $key => $value) {
            $templateBody = str_replace('{' . $key . '}', $value, $templateBody);
        }
        return $templateBody;
    }

}

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        return Setting::get($key, $default);
    }
}