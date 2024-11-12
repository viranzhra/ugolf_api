<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pada file migrasi
        Schema::table('terminals', function (Blueprint $table) {
            $table->boolean('is_active')->default(false); // false = offline, true = online
            $table->timestamp('last_ping')->nullable();   // Simpan waktu ping terakhir
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terminals', function (Blueprint $table) {
            //
        });
    }
};
