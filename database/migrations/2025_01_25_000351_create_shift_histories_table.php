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
        Schema::create('shift_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained()->onDelete('cascade');
            $table->foreignId('table_id')->constrained();
            $table->foreignId('reservation_id')->nullable()->constrained();
            $table->time('planned_time');
            $table->time('actual_time')->nullable();
            $table->enum('status', ['planned', 'seated', 'completed', 'no_show', 'cancelled']);
            $table->json('notes')->nullable();  // Any special circumstances or changes
            $table->timestamps();
            
            // Index for quick lookups
            $table->index(['shift_id', 'status']);
            $table->index(['reservation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_histories');
    }
};
