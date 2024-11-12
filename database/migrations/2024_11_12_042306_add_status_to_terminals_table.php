<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('terminals', function (Blueprint $table) {
            $table->string('status')->default('non-aktif'); // Can be 'active' or 'inactive'
        });
    }

    public function down()
    {
        Schema::table('terminals', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
