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
        Schema::create('configs', function (Blueprint $table) {
            $table->id('config_id');
            $table->unsignedBigInteger('terminal_id');
            $table->unsignedBigInteger('payment_type_id');
            $table->string('config_merchant_id', 100);
            $table->string('config_terminal_id', 100);
            $table->string('config_pos_id', 100);
            $table->string('config_user', 100);
            $table->string('config_password', 100);
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configs');
    }
};
