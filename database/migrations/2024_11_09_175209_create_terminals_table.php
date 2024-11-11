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
        Schema::create('terminals', function (Blueprint $table) {
            // $table->engine = 'InnoDB';
            $table->id('terminal_id');
            $table->unsignedBigInteger('merchant_id');
            $table->string('terminal_code', 100)->unique(); // Kode terminal yang auto-generate
            $table->string('terminal_name', 100);
            $table->text('terminal_address');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps(6); // created_at, updated_at
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('deleted_at', 6)->nullable();

            // Foreign key constraint
            $table->foreign('merchant_id')->references('merchant_id')->on('merchants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terminals');
    }
};
