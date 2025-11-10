<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'settings' => ['required', 'array'],

            // General
            'settings.general_application_name' => ['required', 'string', 'max:100'],
            'settings.general_footer_text' => ['nullable', 'string', 'max:255'],
            'settings.general_language' => ['required', 'string', 'max:50'],
            'settings.general_timezone' => ['required', 'string', 'max:100'],
            'settings.general_date_format' => ['required', 'string', 'max:20'],
            'settings.general_time_format' => ['required', 'string', 'max:20'],

            // Email / SMTP
            'settings.mail_host' => ['nullable', 'string', 'max:191'],
            'settings.mail_port' => ['nullable', 'integer'],
            'settings.mail_username' => ['nullable', 'string', 'max:191'],
            'settings.mail_password' => ['nullable', 'string', 'max:500'],
            'settings.mail_encryption' => ['nullable', 'in:tls,ssl,null'],
            'settings.mail_from_address' => ['nullable', 'email'],
            'settings.mail_from_name' => ['nullable', 'string', 'max:100'],

            // SMS
            'settings.sms_driver' => ['nullable', 'in:twilio,vonage,msg91,null'],

            // Twilio
            'settings.sms_twilio_sid' => ['nullable', 'string', 'max:191'],
            'settings.sms_twilio_token' => ['nullable', 'string', 'max:500'],
            'settings.sms_twilio_from' => ['nullable', 'string', 'max:50'],

            // Vonage
            'settings.sms_vonage_api_key' => ['nullable', 'string', 'max:191'],
            'settings.sms_vonage_api_secret' => ['nullable', 'string', 'max:500'],
            'settings.sms_vonage_from' => ['nullable', 'string', 'max:50'],
        ];
    }
}
