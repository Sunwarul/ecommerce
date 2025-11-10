<?php
// database/seeders/SettingSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set('general_application_name', 'DeltaPOS');
        Setting::set('general_footer_text', 'Copyright© DeltaPOS- 2024');
        Setting::set('general_language', 'English');
        Setting::set('general_timezone', '(GMT/UTC 05:30)Kolkata');
        Setting::set('general_date_format', 'd-m-Y');
        Setting::set('general_time_format', '24 Hours');
    }
}
