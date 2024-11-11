<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up()
    {
        Schema::create('trx', function (Blueprint $table) {
            // $table->engine = 'InnoDB';
            $table->id('trx_id');
            $table->unsignedBigInteger('terminal_id');
            $table->string('trx_code', 100); // Kode transaksi dari frontend
            $table->string('trx_reff', 100); // Referensi transaksi dari backend
            $table->unsignedBigInteger('payment_type_id'); // Jenis pembayaran (QRIS ID)
            $table->integer('amount'); // Harga per tiket
            $table->integer('qty'); // Jumlah tiket yang dibeli
            $table->bigInteger('total_amount'); // Total harga (amount * qty)
            $table->text('paycode'); // Kode QR yang disimpan
            $table->timestamp('expire')->nullable(6); // Waktu expire QR
            $table->timestamp('trx_date')->nullable(6); // Tanggal transaksi
            $table->string('payment_date', 225)->nullable(); // Tanggal pembayaran
            $table->string('payment_name', 225)->nullable(); // Nama Pembayaran
            $table->string('payment_phone', 225)->nullable(); // Nomor Telepon Pembayar
            $table->string('reffnumber', 225)->nullable(); // Nomor referensi pembayaran
            $table->string('issuer_reffnumber', 225)->nullable(); // Nomor referensi issuer
            $table->char('payment_status', 1)->default('P'); // Status pembayaran, 'P' = Pending, 'S' = Success
            $table->unsignedBigInteger('created_by');
            $table->timestamps(6); // created_at, updated_at
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('deleted_at', 6)->nullable();

            // Foreign key constraints
            $table->foreign('terminal_id')->references('terminal_id')->on('terminals')->onDelete('cascade');
            $table->foreign('payment_type_id')->references('payment_type_id')->on('payment_types')->onDelete('cascade');
        });
    }
    // public function up()
    // {
    //     Schema::create('trx', function (Blueprint $table) {
    //         $table->engine = 'InnoDB'; // Memastikan tabel menggunakan InnoDB
    //         // $table->id('trx_id'); // ID transaksi
    //         $table->bigIncrements('trx_id')->unsigned();
    //         $table->unsignedBigInteger('payment_type_id');
    //         $table->foreign('payment_type_id')->references('id')->on('payment_types')->onDelete('cascade');
    //         // $table->foreignId('terminal_id')->constrained('terminals')->onDelete('cascade'); // Relasi dengan tabel terminal
    //         $table->string('trx_code', 100); // Kode transaksi (dari frontend)
    //         $table->string('trx_reff', 100); // Referensi transaksi (dari backend)
    //         // $table->foreignId('payment_type_id')->constrained('payment_types')->onDelete('cascade'); // Relasi dengan tipe pembayaran
    //         $table->integer('amount'); // Harga per tiket
    //         $table->integer('qty'); // Jumlah tiket yang dibeli
    //         $table->bigInteger('total_amount'); // Total harga (amount x qty)
    //         $table->text('paycode'); // Kode QR yang disimpan
    //         $table->timestamp('expire')->nullable(); // Waktu expire QR dari response
    //         // $table->timestamp('trx_date'); // Tanggal transaksi
    //         $table->timestamp('trx_date')->nullable();
    //         $table->string('payment_date', 225)->nullable(); // Tanggal pembayaran (saat transaksi berhasil)
    //         $table->string('payment_name', 225); // Nama pembayar
    //         $table->string('payment_phone', 225); // Nomor telepon pembayar
    //         $table->string('reffnumber', 225)->nullable(); // Referensi nomor dari transaksi
    //         $table->string('issuer_reffnumber', 225)->nullable(); // Nomor referensi dari penerbit
    //         $table->char('payment_status', 1); // Status pembayaran (misal: 'S' untuk sukses, 'P' untuk pending)
    //         $table->integer('created_by'); // ID pengguna yang membuat transaksi
    //         $table->timestamps(6); // Tanggal pembuatan dan pembaruan transaksi
    //         $table->integer('updated_by')->nullable(); // ID pengguna yang memperbarui transaksi
    //         $table->integer('deleted_by')->nullable(); // ID pengguna yang menghapus transaksi
    //         $table->timestamp('deleted_at')->nullable(); // Tanggal penghapusan transaksi
    //     });
    // }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx');
    }
};
