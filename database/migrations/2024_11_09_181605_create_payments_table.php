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
        Schema::create('payment_types', function (Blueprint $table) {
            $table->id('payment_type_id');
            $table->string('payment_type_code', 100)->unique(); // Misalnya: QRIS
            $table->string('payment_type_name', 100); // Nama pembayaran (misalnya QRIS)
            $table->text('description')->nullable(); // Deskripsi tambahan
            $table->unsignedBigInteger('created_by');
            $table->timestamps(6); // created_at, updated_at
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('deleted_at', 6)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_types');;
    }
};
