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
        Schema::table('booking_change_requests', function (Blueprint $table) {
            // Menyimpan array ID addons yang dipilih user (contoh: [1, 3, 5])
            $table->json('new_addons')->nullable()->after('new_package_id');
        });
    }

    public function down(): void
    {
        Schema::table('booking_change_requests', function (Blueprint $table) {
            $table->dropColumn('new_addons');
        });
    }
};
