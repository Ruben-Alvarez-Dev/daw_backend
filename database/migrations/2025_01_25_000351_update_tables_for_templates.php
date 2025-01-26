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
        Schema::table('tables', function (Blueprint $table) {
            $table->foreignId('map_template_id')->after('id')->nullable()->constrained()->onDelete('cascade');
            $table->json('position')->after('capacity')->nullable();  // {x, y}
            $table->date('active_from')->after('position')->nullable();
            $table->date('active_until')->after('active_from')->nullable();
            
            // Index for quick temporal queries
            $table->index(['active_from', 'active_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropForeign(['map_template_id']);
            $table->dropColumn(['map_template_id', 'position', 'active_from', 'active_until']);
            $table->dropIndex(['active_from', 'active_until']);
        });
    }
};
