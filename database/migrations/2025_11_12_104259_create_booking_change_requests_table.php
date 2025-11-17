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
        Schema::create('booking_change_requests', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Booking
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            
            // Data Lama (Snapshot saat request dibuat)
            $table->date('old_event_date')->nullable();
            $table->string('old_event_location')->nullable();
            $table->string('old_event_city')->nullable();

            // Data Baru (Request dari Client)
            $table->date('new_event_date')->nullable();
            $table->string('new_event_location')->nullable();
            $table->string('new_event_city')->nullable();

            // Data Package baru
            

            // Informasi Tambahan
            $table->text('reason')->nullable(); // Alasan perubahan
            
            // Status & Admin
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->text('admin_notes')->nullable(); // Catatan dari admin jika menolak/approve

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_change_requests');
    }
};