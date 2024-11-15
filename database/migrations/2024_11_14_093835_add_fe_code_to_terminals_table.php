<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('terminals', function (Blueprint $table) {
            $table->string('fe_code')->nullable()->after('description'); // Adds the 'fe_code' column after 'description'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('terminals', function (Blueprint $table) {
            $table->dropColumn('fe_code'); // Removes the 'fe_code' column
        });
    }
};
