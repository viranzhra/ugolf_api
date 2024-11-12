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
            'payment_type_code' => 'QRIS',
            'payment_type_name' => 'QRIS',
            'description' => 'Payment method using QRIS',
            'created_by' => 1, // Use a default user or adjust as needed
            'updated_by' => 1,
            'deleted_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

