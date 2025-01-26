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
        Schema::create('map_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('zone');  // salon, terrace
            $table->boolean('is_default')->default(false);
            $table->json('elements');  // walls, windows, surfaces, etc
            $table->timestamps();
            
            // Ensure only one default template per zone
            $table->unique(['zone', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('map_templates');
    }
};
