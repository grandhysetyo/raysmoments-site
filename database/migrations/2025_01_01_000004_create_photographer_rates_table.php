<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('photographer_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photographer_id')->constrained('users')->cascadeOnDelete();
            $table->string('city', 100);
            $table->decimal('base_rate', 12, 2);
            $table->decimal('transport_fee', 12, 2)->nullable();
            $table->date('effective_start')->nullable();
            $table->date('effective_end')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('photographer_rates');
    }
};
