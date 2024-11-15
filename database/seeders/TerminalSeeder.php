<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Terminal;
use App\Models\Merchant;
use Faker\Factory as Faker;

class TerminalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');  // Mengatur locale ke Indonesia

        // Pastikan terdapat merchant dalam tabel merchants
        $merchants = Merchant::all();

        Terminal::create([
            'merchant_id' => 1,
            'terminal_code' => '10010005',
            'terminal_name' => $faker->streetName,
            'terminal_address' => $faker->address,
            'description' => $faker->sentence,
            'status' => 'aktif',
            'created_by' => 1, 
            'updated_by' => 1,
            'deleted_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // if ($merchants->isEmpty()) {
        //     $this->command->info('No merchants found, please seed merchants first.');
        //     return;
        // }

        // foreach (range(1, 2) as $index) {
        //     Terminal::create([
        //         'merchant_id' => $merchants->random()->merchant_id,  // Pilih merchant_id acak dari merchant yang ada
        //         'terminal_code' => 'TM-' . strtoupper($faker->unique()->bothify('??####')), // Contoh kode: TM-AB1234
        //         'terminal_name' => $faker->streetName,
        //         'terminal_address' => $faker->address,
        //         'description' => $faker->sentence,
        //         'status' => $faker->randomElement(['aktif', 'non-aktif']),
        //         'created_by' => 1,  // ID admin atau pengguna default
        //         'updated_by' => 1,  // ID admin atau pengguna default
        //         'deleted_by' => null, // Tidak ada penghapusan
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]);
        // }
    }
}
