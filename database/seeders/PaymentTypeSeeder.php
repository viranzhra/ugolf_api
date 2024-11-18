<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentType;

class PaymentTypeSeeder extends Seeder
{
    public function run()
    {
        // Seed data for QRIS
        PaymentType::create([
            'payment_type_code' => 'PT01',
            'payment_type_name' => 'QRIS',
            'description' => 'Payment method using QRIS',
            'created_by' => 1,
            'updated_by' => 1,
            'deleted_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

