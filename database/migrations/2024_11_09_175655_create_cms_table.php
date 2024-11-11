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
        Schema::create('cms', function (Blueprint $table) {
            // $table->engine = 'InnoDB';
            $table->id('cms_id');
            $table->unsignedBigInteger('terminal_id');
            $table->integer('cms_code')->unique();
            $table->string('cms_name', 100);
            $table->string('cms_value', 225);
            $table->unsignedBigInteger('created_by');
            $table->timestamps(6); // created_at, updated_at
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamp('deleted_at', 6)->nullable();

            // Foreign key constraint
            $table->foreign('terminal_id')->references('terminal_id')->on('terminals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms');
    }
};
