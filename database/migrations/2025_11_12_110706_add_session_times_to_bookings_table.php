<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Tambahkan kolom setelah event_city
            $table->time('session_1_time')->nullable()->after('event_city');
            $table->time('session_2_time')->nullable()->after('session_1_time');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['session_1_time', 'session_2_time']);
        });
    }
};