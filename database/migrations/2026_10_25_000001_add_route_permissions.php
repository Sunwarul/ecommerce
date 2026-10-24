<?php

use App\Http\Middleware\AuthorizeRoutePermission as Map;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $existing = DB::table('permissions')->where('guard_name', 'web')->pluck('name')->all();
        $new = array_diff(Map::allPermissionNames(), $existing);

        foreach ($new as $name) {
            DB::table('permissions')->insert(['name' => $name, 'guard_name' => 'web', 'created_at' => $now, 'updated_at' => $now]);
        }

        $superAdminId = DB::table('roles')->where('name', 'Super Admin')->value('id');
        if ($superAdminId) {
            foreach (DB::table('permissions')->whereIn('name', Map::allPermissionNames())->pluck('id') as $pid) {
                DB::table('role_has_permissions')->insertOrIgnore(['permission_id' => $pid, 'role_id' => $superAdminId]);
            }
        }

        Cache::forget('spatie.permission.cache');
        Cache::forever('auth_version', time());
    }

    public function down(): void
    {
        // Permissions may already be assigned to roles; leave them in place.
    }
};
