<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Config;
use Faker\Factory as Faker;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Config::create([
            'terminal_id' => 1,
            'payment_type_id' => 1,
            'config_merchant_id' => '3200124010015',
            'config_terminal_id' => '10010005',
            'config_pos_id' => NULL,
            'config_user' => NULL,
            'config_password' => NULL,
            'created_by' => 1,
            'created_at' => now(),
        ]);
    }
}
