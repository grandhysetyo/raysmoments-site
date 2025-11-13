<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Salin semua enum LAMA Anda dan tambahkan 'Pending'
        // Sesuaikan daftar ini persis dengan daftar Anda, tambahkan 'Pending'
        DB::statement("
            ALTER TABLE bookings MODIFY COLUMN status 
            ENUM(
                'Awaiting DP',
                'DP Verified',
                'Awaiting Final Payment',
                'Fully Paid',
                'Photographer Assigned',
                'Shooting Completed',
                'Originals Delivered',
                'Edits In Progress',
                'Edits Delivered',
                'Project Closed',
                'Pending'  -- <--- TAMBAHAN BARU
            ) NOT NULL DEFAULT 'Awaiting DP'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         // Rollback (opsional, tapi bagus untuk ada)
         DB::statement("
            ALTER TABLE bookings MODIFY COLUMN status 
            ENUM(
                'Awaiting DP',
                'DP Verified',
                'Awaiting Final Payment',
                'Fully Paid',
                'Photographer Assigned',
                'Shooting Completed',
                'Originals Delivered',
                'Edits In Progress',
                'Edits Delivered',
                'Project Closed'
            ) NOT NULL DEFAULT 'Awaiting DP'
        ");
    }
};