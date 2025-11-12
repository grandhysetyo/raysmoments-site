<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages');
            $table->date('event_date');
            $table->string('event_location');
            $table->string('event_city', 100);
            $table->foreignId('photographer_id')->nullable()->constrained('users');
            $table->decimal('photographer_rate', 12, 2)->nullable();
            $table->decimal('package_price', 12, 2);
            $table->decimal('addons_total', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->enum('status', 
            ['Awaiting DP','DP Verified','Awaiting Final Payment','Fully Paid','Photographer Assigned','Shooting Completed','Originals Delivered','Edits In Progress','Edits Delivered','Project Closed'])->default('Booking');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('bookings');
    }
};
