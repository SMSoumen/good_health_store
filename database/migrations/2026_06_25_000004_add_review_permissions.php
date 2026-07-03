<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    private array $permissions = [
        'view reviews',
        'edit reviews',
        'delete reviews',
    ];

    public function up(): void
    {
        $guard = config('auth.defaults.guard', 'web');

        foreach ($this->permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => $guard]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        $guard = config('auth.defaults.guard', 'web');

        Permission::whereIn('name', $this->permissions)
            ->where('guard_name', $guard)
            ->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
