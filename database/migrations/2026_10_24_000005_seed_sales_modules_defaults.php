<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $permissions = [
        'Report Index',
        'Report Export',
        'Installment Index',
        'Installment Create',
        'Installment Collect',
        'Installment Cancel',
        'Installment Plan Manage',
        'EMI Index',
        'EMI Manage',
        'SMS Index',
        'SMS Send',
        'SMS Settings',
    ];

    public function up(): void
    {
        $now = now();

        // Permissions (attached to Super Admin so existing installs keep full access)
        foreach ($this->permissions as $name) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name, 'guard_name' => 'web'],
                ['created_at' => $now, 'updated_at' => $now]
            );
        }

        $superAdminId = DB::table('roles')->where('name', 'Super Admin')->value('id');
        if ($superAdminId) {
            $ids = DB::table('permissions')->whereIn('name', $this->permissions)->pluck('id');
            foreach ($ids as $pid) {
                DB::table('role_has_permissions')->insertOrIgnore([
                    'permission_id' => $pid,
                    'role_id' => $superAdminId,
                ]);
            }
        }

        // Payment method used for bank card EMI rows
        $emiMethodId = DB::table('payment_methods')->where('name', 'Card EMI')->value('id');
        if (! $emiMethodId) {
            $emiMethodId = DB::table('payment_methods')->insertGetId([
                'name' => 'Card EMI',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        DB::table('settings')->updateOrInsert(['key' => 'emi_payment_method_id'], ['value' => $emiMethodId, 'updated_at' => $now, 'created_at' => $now]);

        // Default SMS templates
        $templates = [
            ['sale_completed', 'Sale completed', 'Dear {customer_name}, thank you for shopping at {shop_name}. Invoice {invoice_no}, Total {total}, Paid {paid}, Due {due}.'],
            ['payment_received', 'Payment received', 'Dear {customer_name}, we received {amount} for invoice {invoice_no}. Remaining due {due}. - {shop_name}'],
            ['installment_created', 'Installment agreement created', 'Dear {customer_name}, your installment plan {agreement_no} is active. {installments} x {installment_amount}, first due {next_due_date}. - {shop_name}'],
            ['installment_reminder', 'Installment reminder (upcoming)', 'Dear {customer_name}, your installment of {installment_amount} for {agreement_no} is due on {next_due_date}. - {shop_name}'],
            ['installment_overdue', 'Installment overdue', 'Dear {customer_name}, your installment of {installment_amount} for {agreement_no} was due on {next_due_date}. Please pay to avoid late fees. - {shop_name}'],
            ['festival', 'Festival greeting', 'Eid Mubarak {customer_name}! Wishing you joy and peace from all of us at {shop_name}.'],
        ];
        foreach ($templates as [$key, $name, $body]) {
            DB::table('sms_templates')->insertOrIgnore([
                'key' => $key, 'name' => $name, 'body' => $body, 'is_active' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        Cache::forget('setting_emi_payment_method_id');
        Cache::forget('spatie.permission.cache');
        Cache::forever('auth_version', time());
    }

    public function down(): void
    {
        DB::table('permissions')->whereIn('name', $this->permissions)->delete();
        DB::table('settings')->where('key', 'emi_payment_method_id')->delete();
    }
};
