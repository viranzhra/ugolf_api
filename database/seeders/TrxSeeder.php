<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trx;
use App\Models\Terminal;
use App\Models\PaymentType;
use Faker\Factory as Faker;
use Carbon\Carbon;

class TrxSeeder extends Seeder
{
    public function run()
    {
        // Menghapus semua data di tabel trx sebelum melakukan seeding
        Trx::truncate();

        $faker = Faker::create('id_ID');  // Mengatur locale ke Indonesia

        // Pastikan terdapat terminal dan payment type dalam tabel
        $terminals = Terminal::all();
        $paymentTypes = PaymentType::all();

        if ($terminals->isEmpty()) {
            $this->command->info('No terminals found, please seed terminals first.');
            return;
        }

        if ($paymentTypes->isEmpty()) {
            $this->command->info('No payment types found, please seed payment types first.');
            return;
        }

        // Seed 100 transactions for testing
        foreach (range(1, 100) as $index) {
            $amount = $faker->numberBetween(10000, 500000); // Harga per transaksi
            $qty = $faker->numberBetween(1, 5); // Jumlah transaksi yang dilakukan
            $totalAmount = $amount * $qty;

            Trx::create([
                'terminal_id' => $terminals->random()->terminal_id, // Pilih terminal_id acak
                'trx_code' => 'TRX-' . strtoupper($faker->bothify('??####')), // Kode transaksi
                'trx_reff' => $faker->uuid, // Referensi transaksi dari backend UUID (Universally Unique Identifier)
                'payment_type_id' => $paymentTypes->random()->payment_type_id, // Pilih payment_type_id acak
                'amount' => $amount, // Harga per transaksi
                'qty' => $qty, // Jumlah item yang dibeli
                'total_amount' => $totalAmount, // Total harga (amount * qty)
                'paycode' => $faker->uuid, // Kode pembayaran (UUID sebagai placeholder)
                'expire' => $faker->dateTimeBetween('now', '+1 hour')->format('Y-m-d H:i:s'), // Waktu expire QR
                'trx_date' => Carbon::now()->format('Y-m-d H:i:s'), // Tanggal transaksi
                'payment_date' => $faker->dateTimeBetween('-1 day', 'now')->format('Y-m-d H:i:s'), // Tanggal pembayaran
                'payment_name' => $faker->name, // Nama Pembayar
                'payment_phone' => $faker->phoneNumber, // Nomor Telepon Pembayar
                'reffnumber' => $faker->uuid, // Nomor referensi pembayaran
                'issuer_reffnumber' => $faker->uuid, // nomor referensi yang diberikan oleh issuer (pihak penerbit) dalam konteks transaksi
                'payment_status' => $faker->randomElement(['P', 'S']), // Status pembayaran (Pending / Success)
                'created_by' => 1, // ID admin atau pengguna default
                'updated_by' => 1, // ID admin atau pengguna default
                'deleted_by' => null, // Tidak ada penghapusan
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Trx::create([
            //     'terminal_id' => $terminals->random()->terminal_id, // Pilih terminal_id acak
            //     'trx_code' => 'TRX-' . strtoupper($faker->bothify('??####')), // Kode transaksi
            //     'trx_reff' => $faker->uuid, // Referensi transaksi dari backend (UUID)
            //     'payment_type_id' => $paymentTypes->random()->payment_type_id, // Pilih payment_type_id acak
            //     'amount' => $amount, // Harga per transaksi
            //     'qty' => $qty, // Jumlah item yang dibeli
            //     'total_amount' => $totalAmount, // Total harga (amount * qty)
            //     'paycode' => $faker->uuid, // Kode pembayaran (UUID sebagai placeholder)
            //     'expire' => $faker->dateTimeBetween('now', '+1 hour')->format('Y-m-d H:i:s'), // Waktu expire QR
            //     'trx_date' => Carbon::now()->format('Y-m-d H:i:s'), // Tanggal transaksi
            //     'payment_date' => $faker->dateTimeBetween('-1 day', 'now')->format('Y-m-d H:i:s'), // Tanggal pembayaran
            //     'payment_status' => $faker->randomElement(['P', 'S']), // Status pembayaran (Pending / Success)
            //     'created_by' => 1, // ID admin atau pengguna default
            //     'updated_by' => 1, // ID admin atau pengguna default
            //     'deleted_by' => null, // Tidak ada penghapusan
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);
        }
    }
}
