<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cms;
use Faker\Factory as Faker;

class CmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create('id_ID');

        Cms::create([
            'terminal_id' => 1,
            'cms_code' => 1,
            'cms_name' => 'price',
            'cms_value' => '100',
            'created_by' => 1,
        ]);

        // Pastikan terdapat merchant dalam tabel cms
        // $cms = Cms::all();

        // if ($cms->isEmpty()) {
        //     $this->command->info('No cms found, please seed cms first.');
        //     return;
        // }

        // foreach (range(1, 10) as $index) {
        //     Cms::create([
        //         'terminal_id' => $faker->randomElement($cms)->terminal_id, // Mengambil terminal_id yang sesuai dengan merchant
        //         'cms_code' => $faker->unique()->numberBetween(100000, 999999),  // untuk menghasilkan angka acak
        //         'cms_name' => $faker->word, // Nama CMS, bisa disesuaikan (misal: tiket, logo, dsb)
        //         'cms_value' => $faker->word, // Nilai CMS, bisa disesuaikan
        //         'created_by' => 1, // ID pengguna yang membuat data, bisa diganti dengan ID user yang sesuai
        //     ]);
        // }
    }
}
