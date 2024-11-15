<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Merchant;
use Faker\Factory as Faker;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $faker = Faker::create();

        Merchant::create([
            'merchant_code' => '3200124010015',
            'merchant_name' => 'Merchant One',
            'merchant_address' => 'Jl. Merchant No. 41 Street',
            'description' => 'Merchant One Description',
            'created_by' => 1,  // ID admin
            'updated_by' => 1,  // ID admin
            'deleted_by' => null, // Tidak ada penghapusan
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // $faker = Faker::create('id_ID');

        // foreach (range(1, 2) as $index) {
        //     Merchant::create([
        //         'merchant_code' => $faker->unique()->numerify('MC-####'),
        //         'merchant_name' => $faker->company,
        //         'merchant_address' => $faker->address,
        //         'description' => $faker->sentence,
        //         'created_by' => 1,  // ID admin
        //         'updated_by' => 1,  // ID admin
        //         'deleted_by' => null, // Tidak ada penghapusan
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]);
        // }
    }
}
