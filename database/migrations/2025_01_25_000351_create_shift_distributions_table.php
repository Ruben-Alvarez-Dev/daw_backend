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
        Schema::create('shift_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained()->onDelete('cascade');
            $table->foreignId('map_template_id')->constrained();
            $table->string('zone');
            $table->json('table_positions');  // Custom positions for this shift
            $table->timestamps();
            
            // Each shift can only have one distribution per zone
            $table->unique(['shift_id', 'zone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_distributions');
    }
};
