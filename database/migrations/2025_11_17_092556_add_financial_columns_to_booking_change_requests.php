<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_change_requests', function (Blueprint $table) {
            // Menyimpan ID Paket Baru yang diinginkan
            $table->foreignId('new_package_id')->nullable()->after('booking_id')->constrained('packages')->onDelete('set null');
            
            // Menyimpan ESTIMASI biaya tambahan (Draft Tagihan)
            // Disimpan di sini dulu, jangan langsung ke tabel payments
            $table->decimal('additional_cost', 15, 2)->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('booking_change_requests', function (Blueprint $table) {
            $table->dropForeign(['new_package_id']);
            $table->dropColumn(['new_package_id', 'additional_cost']);
        });
    }
};