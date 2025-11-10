<?php

// app/Http/Controllers/SettingsController.php
namespace App\Http\Controllers;

use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function general()
    {
        $settings = [
            // General
            'general_application_name' => Setting::get('general_application_name', 'DeltaPOS'),
            'general_footer_text' => Setting::get('general_footer_text', 'Copyright© DeltaPOS- 2024'),
            'general_language' => Setting::get('general_language', 'English'),
            'general_timezone' => Setting::get('general_timezone', '(GMT/UTC 05:30)Kolkata'),
            'general_date_format' => Setting::get('general_date_format', 'd-m-Y'),
            'general_time_format' => Setting::get('general_time_format', '24 Hours'),

            // Mail
            'mail_host' => Setting::get('mail_host'),
            'mail_port' => Setting::get('mail_port', 587),
            'mail_username' => Setting::get('mail_username'),
            'mail_password' => Setting::get('mail_password') ? '******' : '', // mask
            'mail_encryption' => Setting::get('mail_encryption', 'tls'),
            'mail_from_address' => Setting::get('mail_from_address'),
            'mail_from_name' => Setting::get('mail_from_name', 'DeltaPOS'),

            // SMS
            'sms_driver' => Setting::get('sms_driver', 'twilio'),
            'sms_twilio_sid' => Setting::get('sms_twilio_sid'),
            'sms_twilio_token' => Setting::get('sms_twilio_token') ? '******' : '',
            'sms_twilio_from' => Setting::get('sms_twilio_from'),
            'sms_vonage_api_key' => Setting::get('sms_vonage_api_key'),
            'sms_vonage_api_secret' => Setting::get('sms_vonage_api_secret') ? '******' : '',
            'sms_vonage_from' => Setting::get('sms_vonage_from'),
        ];

        return Inertia::render('Admin/Settings/General', compact('settings'));
    }

    public function update(UpdateSettingsRequest $request)
    {
        foreach ($request->validated()['settings'] as $key => $value) {
            // Skip masked secrets
            if (
                in_array($key, ['mail_password', 'sms_twilio_token', 'sms_vonage_api_secret'], true)
                && $value === '******'
            ) {
                continue;
            }
            Setting::set($key, $value);
        }
        return back()->with('success', 'Settings saved.');
    }
}
