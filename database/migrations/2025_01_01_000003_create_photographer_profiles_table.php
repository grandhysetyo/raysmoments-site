<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('photographer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photographer_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('default_rate', 12, 2)->nullable();
            $table->integer('experience_years')->nullable();
            $table->string('speciality')->nullable();
            $table->text('bio')->nullable();
            $table->text('portfolio_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('photographer_profiles');
    }
};
