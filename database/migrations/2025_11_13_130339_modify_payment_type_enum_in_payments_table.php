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
        // Tambahkan 'Refund' ke daftar ENUM
        DB::statement("
            ALTER TABLE payments MODIFY COLUMN payment_type 
            ENUM('DP', 'Final','AddOn','Refund') 
            NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembali ke enum lama
        DB::statement("
            ALTER TABLE payments MODIFY COLUMN payment_type 
            ENUM('DP', 'Final','AddOn') 
            NOT NULL
        ");
    }
};