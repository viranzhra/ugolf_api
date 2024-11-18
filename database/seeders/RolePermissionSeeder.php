<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'merchant.view', 'merchant.create', 'merchant.edit', 'merchant.delete',
            'terminal.view', 'terminal.create', 'terminal.edit', 'terminal.delete',
            'cms.view', 'cms.create', 'cms.edit', 'cms.delete',
            'payment type.view', 'payment type.create', 'payment type.edit', 'payment type.delete',
            'config.view', 'config.create', 'config.edit', 'config.delete',
            'transaction.view', 'transaction.create', 'transaction.edit', 'transaction.delete',
            'role.view', 'role.create', 'role.edit', 'role.delete',
        ];

        // Membuat permissions jika belum ada
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Buat role Admin dan tambahkan semua izin
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());

        // Buat role Customer dengan izin terbatas
        // $customerRole = Role::firstOrCreate(['name' => 'Customer', 'guard_name' => 'web']);
        // $customerRole->givePermissionTo(['item.view', 'item request.viewFilterbyUser', 'item request.create']);

        // Buat user Admin dan berikan role Admin
        $admin = User::find(1);
        $admin->assignRole($adminRole);

            // // Buat user Customer dan berikan role Customer
            // for($i = 2; $i <= 6; $i++) {
            //     $customer = User::find($i);
            //     if($customer) {
            //         $customer->assignRole($customerRole);
            //     }
            // }    
        }
}
