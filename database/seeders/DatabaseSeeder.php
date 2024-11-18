<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            // 'role' => 'admin', // Pastikan ada kolom 'role' di tabel users
        ]);

        // User::factory()->create([
        //     'name' => 'User',
        //     'email' => 'user@gmail.com',
        //     'password' => Hash::make('user123'),
        //     // 'role' => 'user', // Pastikan ada kolom 'role' di tabel users
        // ]);

        $this->call(MerchantSeeder::class);
        $this->call(TerminalSeeder::class);
        $this->call(CmsSeeder::class);
        $this->call(PaymentTypeSeeder::class);
        $this->call(ConfigSeeder::class);
        $this->call(RolePermissionSeeder::class);
    }
}
