<?php

namespace App\Support;

use App\Models\Setting;

class SettingLoader
{
    public static function load(): void
    {
        // MAIL
        if ($host = Setting::get('mail_host')) {
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $host,
                'mail.mailers.smtp.port' => (int) Setting::get('mail_port', 587),
                'mail.mailers.smtp.username' => Setting::get('mail_username'),
                'mail.mailers.smtp.password' => Setting::get('mail_password'),
                'mail.mailers.smtp.encryption' => ($e = Setting::get('mail_encryption', 'tls')) === 'null' ? null : $e,
                'mail.from.address' => Setting::get('mail_from_address', 'no-reply@example.com'),
                'mail.from.name' => Setting::get('mail_from_name', 'DeltaPOS'),
            ]);
        }

        // SMS driver
        $driver = Setting::get('sms_driver', 'twilio');
        config(['services.sms.driver' => $driver]);

        if ($driver === 'twilio') {
            config([
                'services.twilio.sid' => Setting::get('sms_twilio_sid'),
                'services.twilio.token' => Setting::get('sms_twilio_token'),
                'services.twilio.from' => Setting::get('sms_twilio_from'),
            ]);
        }

        if ($driver === 'vonage') {
            config([
                'services.vonage.key' => Setting::get('sms_vonage_api_key'),
                'services.vonage.secret' => Setting::get('sms_vonage_api_secret'),
                'services.vonage.from' => Setting::get('sms_vonage_from'),
            ]);
        }
    }
}
